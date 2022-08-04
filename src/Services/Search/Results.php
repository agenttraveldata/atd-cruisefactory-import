<?php


namespace ATD\CruiseFactory\Services\Search;


use ATD\CruiseFactory\Post;
use ATD\CruiseFactory\Services\WordPress\Posts\Hydrator;
use ATD\CruiseFactory\Taxonomy;
use DateTime;
use WP_Query;
use WP_REST_Request;

class Results {
	private ?Hydrator $hydrator;

	public function __construct( ?Hydrator $hydrator = null ) {
		$this->hydrator = $hydrator;
	}

	public function searchQuery( WP_Query $query ): void {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive && isset( $query->query['post_type'] ) ) {
			switch ( $query->query['post_type'] ) {
				case Post\Departure::$postType:
					$this->setupSearchQuery( $query );
					break;
				case Post\Ship::$postType:
				case Post\CruiseLine::$postType:
				case Post\Destination::$postType:
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'ASC' );
					break;
			}
		} elseif ( isset( $query->query[ Taxonomy\PromoCode::$name ] ) || isset( $query->query[ Taxonomy\SpecialType::$name ] ) || isset( $query->query[ Taxonomy\DepartureType::$name ] ) ) {
			$this->setupSearchQuery( $query );
		}
	}

	public function restDepartureQuery( array $args, WP_REST_Request $request ): array {
		define( 'ATD_CF_XML_HYDRATE_POST', true );

		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = [];
		}

		$args['meta_query'][] = [
			'key'     => 'atd_cfi_sailing_date',
			'value'   => ( new DateTime() )->getTimestamp(),
			'compare' => '>='
		];

		$newQuery = new WP_Query();
		$this->setupSearchQuery( $newQuery, $request );

		if ( isset( $newQuery->query_vars['tax_query'] ) ) {
			$args['tax_query'] = $newQuery->query_vars['tax_query'];
		}
		if ( isset( $newQuery->query_vars['meta_query'] ) ) {
			$args['meta_query'] = $newQuery->query_vars['meta_query'];
		}
		if ( isset( $newQuery->query_vars['s'] ) ) {
			$args['s'] = $newQuery->query_vars['s'];
		}

		$args['orderby'] = [ 'atd_cfi_sailing_date' => 'ASC' ];

		$newQuery->set( 'fields', 'ids' );

		if ( $ids = $newQuery->get_posts() ) {
			$newQuery->posts = [];

			foreach ( $ids as $id ) {
				$post            = new \stdClass();
				$post->ID        = $id;
				$post->post_type = Post\Departure::$postType;

				$newQuery->posts[] = new \WP_Post( $post );
			}
		}

		$this->hydrator->loopStart( $newQuery );
		unset( $newQuery );

		return $args;
	}

	private function setupSearchQuery( WP_Query $query, WP_REST_Request $request = null ): void {
		$query->set( 'post_type', 'departure' );
		$query->set( 'post_status', 'public' );
		$query->set( 'posts_per_page', get_option( 'posts_per_page' ) );

		$query->set( 'meta_query', [
			'relation'             => 'AND',
			'atd_cfi_sailing_date' => $this->getMetaArray( 'sailing_date', ( new DateTime() )->setTime( 0, 0 )->getTimestamp(), '>=' )
		] );
		$query->set( 'orderby', [ 'atd_cfi_sailing_date' => 'ASC' ] );

		if ( isset( $_GET ) && sizeof( $_GET ) > 0 ) {
			if ( isset( $query->query[ Taxonomy\DepartureType::$name ] ) && $query->query[ Taxonomy\DepartureType::$name ] === 'special' ) {
				$_GET['offerType_id'] = 'special';
			}
			if ( ! empty( $_GET['page'] ) ) {
				$query->set( 'paged', (int) $_GET['page'] );
			}

			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
                $queryParams = $request->get_query_params()['atd_cf_filter'] ?? [];
				$this->searchQueryByTaxonomy( $query, $queryParams );
			}

			$this->searchQueryDateAndKeywords( $query, $_GET );
		} elseif ( isset( $query->query[ Taxonomy\SpecialType::$name ] ) ) {
			$GLOBALS['showing-top-deals'] = true;

			$query->set( 'no_found_rows', true );
			$query->set( 'meta_query', array_merge( [
				'atd_cfi_special_order' => $this->getMetaArray( 'special_order', '>' )
			], $query->get( 'meta_query' ) ) );
			$query->set( 'orderby', array_merge( [ 'atd_cfi_special_order' => 'DESC' ], $query->get( 'orderby' ) ) );
		}

		remove_action( 'pre_get_posts', [ $this, 'searchQuery' ] );
	}

	private function searchQueryDateAndKeywords( WP_Query $query, array $criteria ): void {
        $meta = [[
            'key'     => 'atd_cfi_sailing_date',
            'value'   => (new DateTime())->getTimestamp(),
            'type'    => 'NUMERIC',
            'compare' => '>='
        ]];

		foreach ( $criteria as $criterion => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			switch ( $criterion ) {
				case Taxonomy\Month::$name . '_from':
					if ( $dateValue = DateTime::createFromFormat( 'Ymd', $value . '01' ) ) {
						$meta[] = $this->getMetaArray( 'sailing_date', $dateValue->setTime( 0, 0 )->getTimestamp(), '>=' );
					}
					break;
				case Taxonomy\Month::$name . '_to':
					if ( $dateValue = DateTime::createFromFormat( 'Ymd', $value . '01' ) ) {
						/* get last day of month */
						$dateValue = DateTime::createFromFormat( 'Ymd', $dateValue->format( 'Ymt' ) );

						$meta[] = $this->getMetaArray( 'sailing_date', $dateValue->setTime( 0, 0 )->getTimestamp(), '<=' );
					}
					break;
				case 'atd_cf_keyword':
					$query->set( 's', $value );
					break;
			}
		}

		if ( ! empty( $meta ) ) {
			$query->set( 'meta_query', [
				'relation' => 'AND',
				$meta
			] );
		}
	}

	private function searchQueryByTaxonomy( WP_Query $query, array $criteria ): void {
		$tax = $meta = [];

		$taxonomies = array_flip( Taxonomy\Provider::$taxonomies );
		array_walk( $taxonomies, function ( &$value, $className ) {
			$value = $className::$name;
		} );
		$taxonomies = array_flip( $taxonomies );

		foreach ( $criteria as $criterion => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			switch ( $criterion ) {
				case Taxonomy\Month::$name . '_from':
				case Taxonomy\Month::$name . '_to':
				case 'atd_cf_keyword':
					$_GET[ $criterion ] = $value;
					break;
				default:
					if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $taxonomies[ $criterion ] ) ) {
						$tax[] = $this->getTaxonomyArray( $criterion, $value );
					}
			}
		}

		if ( ! empty( $tax ) ) {
			$query->set( 'tax_query', array_merge( [ 'relation' => 'AND' ], $tax ) );
		}

		if ( ! empty( $meta ) ) {
			$query->set( 'meta_query', [
				'relation' => 'AND',
				$meta
			] );
		}
	}

	private function getMetaArray( string $key, $value, string $compare = '=' ): array {
		return [
			'key'     => 'atd_cfi_' . $key,
			'value'   => $value,
			'compare' => $compare
		];
	}

	private function getTaxonomyArray( string $taxonomy, $terms ): array {
		if ( ! is_array( $terms ) ) {
			$terms = [ $terms ];
		}

		return [
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $terms
		];
	}
}