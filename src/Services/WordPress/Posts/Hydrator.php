<?php

namespace ATD\CruiseFactory\Services\WordPress\Posts;

use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Post;
use ATD\CruiseFactory\Services\Data\DBAL\EntityManager;
use ATD\CruiseFactory\Taxonomy;
use mysqli;
use WP_Post;
use WP_Query;
use WP_REST_Response;

class Hydrator {
	private mysqli $dbh;
	private EntityManager $entityManager;
	private array $postDetails = [];
	private array $globalsToUnset = [];

	public function __construct( mysqli $dbh ) {
		$this->dbh           = $dbh;
		$this->entityManager = new EntityManager( $this->dbh );
	}

	public function loopStart( WP_Query $query ) {
		if ( ! $query->is_main_query() && ! ( $query->post_count > 0 ) && ! defined( 'ATD_CF_XML_HYDRATE_POST' ) ) {
			return;
		}

		$fetch           = [];
		$validPostTypes  = [
			Post\Departure::$postType,
			Post\Destination::$postType,
			Post\CruiseLine::$postType,
			Post\Ship::$postType
		];
		$validTaxonomies = [
			Taxonomy\DepartureType::$name,
			Taxonomy\Destination::$name,
			Taxonomy\CruiseLine::$name,
			Taxonomy\Ship::$name,
			Taxonomy\Month::$name,
			Taxonomy\Duration::$name,
		];

		if ( ( $query->is_post_type_archive( $validPostTypes ) && isset( $query->query['post_type'] )
		       || ( $query->is_tax( $validTaxonomies ) ) ) ) {
			$this->setFactoryGlobalVariable();

			foreach ( array_column( $query->posts, 'ID' ) as $id ) {
				if ( $meta = get_metadata_raw( 'post', $id ) ) {
					$meta = array_map( 'reset', $meta );

					/** @var ?string $entityClass */
					$entityClass = null;

					if ( $query->is_tax ) {
						$query->query['post_type'] = Post\Departure::$postType;
					}

					switch ( $query->query['post_type'] ) {
						case Post\Departure::$postType:
							$entityClass = Entity\Departure::class;
							$metaKeyId   = Feed\Departure::$metaKeyId;

							if ( isset( $meta[ Feed\SpecialDeparture::$metaKeyId ] ) ) {
								$entityClass = Entity\SpecialDeparture::class;
								$metaKeyId   = Feed\SpecialDeparture::$metaKeyId;
							}
							break;
						case Post\Destination::$postType:
							$entityClass = Entity\Destination::class;
							$metaKeyId   = Feed\Destination::$metaKeyId;
							break;
						case Post\Ship::$postType:
							$entityClass = Entity\Ship::class;
							$metaKeyId   = Feed\Ship::$metaKeyId;
							break;
						case Post\CruiseLine::$postType:
							$entityClass = Entity\CruiseLine::class;
							$metaKeyId   = Feed\CruiseLine::$metaKeyId;
							break;
					}

					if ( ! empty( $entityClass ) && ! empty( $metaKeyId ) ) {
						$fetch[ $entityClass ][] = [ $id, $meta[ $metaKeyId ] ];
					}
				}
			}

			foreach ( $fetch as $entityClass => $details ) {
				if ( $rows = $this->entityManager->getMapper( $entityClass )->findBy( [ 'id' => array_column( $details, 1 ) ] ) ) {
					foreach ( $rows as $row ) {
						$this->postDetails[ $details[ array_search( $row->getId(), array_column( $details, 1 ) ) ][0] ] = $row;
					}
				}
			}
		} elseif ( $query->is_singular( $validPostTypes ) ) {
			$this->setFactoryGlobalVariable();

			if ( $postClass = Post\Provider::getPostClassByPostType( $query->post->post_type ) ) {
				switch ( $postClass ) {
					case Post\Departure::class:
						$entityClass = Entity\Departure::class;
						$metaKeyId   = Feed\Departure::$metaKeyId;

						if ( get_metadata_raw( 'post', $query->post->ID, Feed\SpecialDeparture::$metaKeyId, true ) ) {
							$entityClass = Entity\SpecialDeparture::class;
							$metaKeyId   = Feed\SpecialDeparture::$metaKeyId;
						}
						break;
					default:
						$entityClass = str_replace( '\Post', '\Entity', $postClass );
						/** @var Feed\Feed $feedClass */
						$feedClass = str_replace( '\Entity', '\Feed', $entityClass );
						$metaKeyId = $feedClass::$metaKeyId;
				}

				if ( $id = get_metadata_raw( 'post', $query->post->ID, $metaKeyId, true ) ) {
					$this->postDetails[ $query->post->ID ] = $this->entityManager->getMapper( $entityClass )->find( $id );
				}
			}
		}
	}

	public function thePost( WP_Post $post ) {
		switch ( $post->post_type ) {
			case Post\Departure::$postType:
			case Post\Destination::$postType:
			case Post\Ship::$postType:
			case Post\CruiseLine::$postType:
				$details = $this->postDetails[ $post->ID ] ?? null;
				break;
			default:
				return;
		}

		if ( ! $details ) {
			return;
		}

		$entityClass = get_class( $details );

		switch ( $entityClass ) {
			case Entity\SpecialDeparture::class:
				$details->getSpecial()->setDepartureId( $details->getId() );
				$this->setGlobalVariable( 'special', $details->getSpecial() );
				$this->setGlobalVariable( 'departure', $details->getSailingDate() );

				$this->globalsToUnset = [ 'special', 'departure' ];
				break;
			default:
				$globalName = substr( strrchr( $entityClass, '\\' ), 1 );
				$this->setGlobalVariable( $globalName, $details );

				$this->globalsToUnset = [ $globalName ];
		}
	}

	public function restDepartureHydration( WP_REST_Response $response ): WP_REST_Response {
		if ( ! defined( 'ATD_CF_XML_HYDRATE_POST' ) ) {
			return $response;
		}

		ob_start();
		atd_cf_get_template_part( 'content/content', 'result' );
		$html = ob_get_clean();

		$response->set_data( array_merge( $response->get_data(), [ 'html_response' => $html ] ) );

		return $response;
	}

	private function setGlobalVariable( string $name, $value ) {
		foreach ( $this->globalsToUnset as $unsetName ) {
			unset( $GLOBALS[ $this->getGlobalVariableName( $unsetName ) ] );
		}

		if ( ! empty( $value ) ) {
			$GLOBALS[ $this->getGlobalVariableName( $name ) ] = $value;
		}
	}

	private function getGlobalVariableName( string $name ): string {
		return 'atd' . ucfirst( $name );
	}

	private function setFactoryGlobalVariable(): void {
		if ( $factory = $this->entityManager->getMapper( Entity\Factory::class )->findBy( [ 'id >' => 0 ] ) ) {
			$this->setGlobalVariable( 'factory', reset( $factory ) );
		}
	}
}