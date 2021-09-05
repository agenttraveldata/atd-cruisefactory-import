<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity\SpecialDeparture;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use ATD\CruiseFactory\Services\WordPress\Blocks;
use ATD\CruiseFactory\Taxonomy;
use WP_Post;
use WP_Query;

class Departure implements Post {
	public static string $postType = 'departure';
	private static string $cfImageUrl = 'https://ik.imagekit.io/atd/cruises/';

	public static function add( object $details ): ?int {
		if ( $details instanceof SpecialDeparture ) {
			$specialDepartureId = $details->getId();
			$special            = $details->getSpecial();

			if ( empty( $special ) || ! $details = $details->getSailingdate() ) {
				return null;
			}
		}

		if ( ! empty( $specialDepartureId ) && $originalPost = self::findOriginalPost( [
				'relation'             => 'AND',
				'special_departure_id' => [
					'key'   => Feed\SpecialDeparture::$metaKeyId,
					'value' => $specialDepartureId
				]
			] ) ) {
			$originalType = 'special';
		} else if ( $originalPost = self::findOriginalPost( [
			'relation'     => 'AND',
			'departure_id' => [
				'key'   => Feed\Departure::$metaKeyId,
				'value' => $details->getId()
			]
		] ) ) {
			if ( get_metadata_raw( 'post', $originalPost->ID, Feed\SpecialDeparture::$metaKeyId, true ) ) {
				return $originalPost->ID;
			}

			$originalType = 'cruise';
		} else {
			$originalType = null;
		}

		if ( ! defined( 'ATD_CF_XML_IMPORT_FORCE_OVERWRITE' ) ) {
			if ( $originalPost && $originalPost->post_author > 0 ) {
				return $originalPost->ID;
			}
		}

		$postTitle = isset( $special )
			? $special->getName()
			: $details->getCruise()->getName() . ' - ' . $details->getSailingDate()->format( 'd/m/Y' );

		$post_details = [
			'post_type'    => self::$postType,
			'post_title'   => $postTitle,
			'post_name'    => sanitize_title( $postTitle ),
			'post_content' => ( new Blocks\Paragraph( nl2br( trim( $details->getCruise()->getDescription() ) ) ) )->render(),
			'post_excerpt' => $details->getCruise()->getBriefDescription(),
			'post_status'  => 'publish'
		];

		$meta_input = [
			Feed\Departure::$metaKeyId => $details->getId(),
			'atd_cfi_cruise_id'        => $details->getCruise()->getId(),
			'atd_cfi_sailing_date'     => $details->getSailingdate()->getTimestamp(),
			'atd_cfi_start_price'      => ! empty( $special ) ? $special->getStartPrice() : $details->getStartPrice()
		];

		if ( ! empty( $special ) && ! empty( $specialDepartureId ) ) {
			$meta_input[ Feed\SpecialDeparture::$metaKeyId ] = $specialDepartureId;
			$meta_input['atd_cfi_special_id']                = $special->getId();
			$meta_input['atd_cfi_valid_from']                = $special->getValidFrom()->getTimestamp();
			$meta_input['atd_cfi_valid_to']                  = $special->getValidTo()->getTimestamp();
		}

		$post_details['meta_input'] = $meta_input;

		if ( $originalPost ) {
			$post_details['ID'] = $originalPost->ID;
		}

		switch ( $originalType ) {
			case 'special':
				if ( ! empty( $specialDepartureId ) ) {
					Logger::modify( sprintf( '[%d] Updated special %s post %s', $meta_input['atd_cfi_id'], $post_details['post_type'], $originalPost->post_title ) );
				} else {
					Logger::modify( sprintf( '[%d] Converted special %s post %s to cruise %s', $meta_input['atd_cfi_id'], $post_details['post_type'], $originalPost->post_title, $post_details['post_title'] ) );
				}
				break;
			case 'cruise':
				if ( ! empty( $specialDepartureId ) ) {
					Logger::modify( sprintf( '[%d] Converted cruise %s post %s to special %s', $meta_input['atd_cfi_id'], $post_details['post_type'], $originalPost->post_title, $post_details['post_title'] ) );
				} else {
					Logger::modify( sprintf( '[%d] Updated cruise %s post %s', $meta_input['atd_cfi_id'], $post_details['post_type'], $originalPost->post_title ) );
				}
				break;
			default:
				Logger::add( sprintf( '[%d] Added %s post %s', $meta_input['atd_cfi_id'], $post_details['post_type'], $post_details['post_title'] ) );
		}

		if ( ! empty( $originalPost ) && empty( $specialDepartureId ) ) {
			/*
			 * Only cruises will keep title and update, specials will
			 * be fully replaced
			*/
			unset( $post_details['post_title'] );
			$post_id = wp_update_post( $post_details );
		} else {
			$post_id = wp_insert_post( $post_details );
		}

		if ( $term = Taxonomy\Destination::addTerm( Taxonomy\Destination::$name, $details->getCruise()->getDestination()->getName(), (string) $details->getCruise()->getDestination()->getId() ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\Destination::$name );
		}

		if ( $term = Taxonomy\CruiseLine::addTerm( Taxonomy\CruiseLine::$name, $details->getCruise()->getCruiseLine()->getName(), (string) $details->getCruise()->getCruiseLine()->getId() ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\CruiseLine::$name );
		}

		if ( $term = Taxonomy\Ship::addTerm( Taxonomy\Ship::$name, $details->getCruise()->getShip()->getName(), (string) $details->getCruise()->getShip()->getId() ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\Ship::$name );
		}

		if ( $term = Taxonomy\DepartureType::addTerm( Taxonomy\DepartureType::$name, isset( $special ) ? 'Special' : 'Cruise' ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\DepartureType::$name );
		}

		if ( ! empty( $specialDepartureId ) && ! empty( $special ) ) {
			if ( $term = Taxonomy\SpecialType::addTerm( Taxonomy\SpecialType::$name, $special->getType() ) ) {
				wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\SpecialType::$name );
			}
		}

		if ( $term = Taxonomy\Duration::addTerm(
			Taxonomy\Duration::$name, $details->getCruise()->getDurationTerm() . ' nights',
			str_replace( [ '-', '+' ], [ '', '00' ], $details->getCruise()->getDurationTerm() ) )
		) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\Duration::$name );
		}

		if ( $term = Taxonomy\EmbarkPort::addTerm( Taxonomy\EmbarkPort::$name, $details->getCruise()->getEmbarkPort()->getName(), (string) $details->getCruise()->getEmbarkPort()->getId() ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\EmbarkPort::$name );
		}

		if ( $term = Taxonomy\DisembarkPort::addTerm( Taxonomy\DisembarkPort::$name, $details->getCruise()->getDisembarkPort()->getName(), (string) $details->getCruise()->getDisembarkPort()->getId() ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\DisembarkPort::$name );
		}

		if ( $term = Taxonomy\Month::addTerm( Taxonomy\Month::$name, $details->getSailingDate()->format( 'F Y' ), $details->getSailingDate()->format( 'Ym' ) ) ) {
			wp_set_object_terms( $post_id, (int) $term['term_id'], Taxonomy\Month::$name );
		}

		if ( ! empty( $details->getCruise()->getPhoto() ) ) {
			$imageUrl      = self::$cfImageUrl . $details->getCruise()->getPhoto();
			$imageFileName = 'atd-cfi_cruise-' . $details->getCruise()->getId() . ( pathinfo( $imageUrl, PATHINFO_EXTENSION ) === '' ? '.jpg' : '' );

			if ( has_post_thumbnail( $post_id ) ) {
				if ( defined( 'ATD_CF_XML_IMAGE_OVERWRITE' ) ) {
					$thumb     = get_the_post_thumbnail_url( $post_id );
					$uploadDir = wp_get_upload_dir();
					$subPath   = str_replace( $uploadDir['baseurl'], '', $thumb );
					$thumbPath = $uploadDir['basedir'] . $subPath;
					file_put_contents( $thumbPath, file_get_contents( $imageUrl ) );
				}

				return true;
			}

			$attachmentMetaKey = 'atd_cfi_cruise_id';

			$attachment = new WP_Query( [
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'nopaging'       => true,
				'no_found_rows'  => true,
				'meta_query'     => [
					'relation' => 'AND',
					'id'       => [
						'key'   => $attachmentMetaKey,
						'value' => $details->getCruise()->getId()
					]
				]
			] );

			if ( $attachment->post_count === 1 ) {
				set_post_thumbnail( $post_id, $attachment->post->ID );
			} else {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );

				$file = [
					'name'     => $imageFileName,
					'tmp_name' => download_url( $imageUrl )
				];

				if ( ! is_wp_error( $file['tmp_name'] ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );

					$attachmentPostData = [
						'post_title'  => 'Map image for cruise ID ' . $details->getCruise()->getId(),
						'post_status' => 'publish',
						'meta_input'  => [
							$attachmentMetaKey => $details->getCruise()->getId()
						]
					];

					$id = media_handle_sideload( $file, $post_id, null, $attachmentPostData );

					if ( is_wp_error( $id ) ) {
						@unlink( $file['tmp_name'] );
					} else {
						set_post_thumbnail( $post_id, $id );
					}
				}
			}
		}

		return true;
	}

	private static function findOriginalPost( $metaQuery ): ?WP_Post {
		$originalPost = new WP_Query( [
			'post_type'      => self::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_query'     => $metaQuery
		] );

		if ( $originalPost->post_count !== 0 ) {
			return $originalPost->post;
		}

		return null;
	}

	public static function convertBackToCruise( SpecialDeparture $details ): bool {
		if ( $originalPost = self::findOriginalPost( [
			'relation'             => 'AND',
			'special_departure_id' => [
				'key'   => Feed\SpecialDeparture::$metaKeyId,
				'value' => $details->getId()
			],
			'special_id_exists'    => [
				'key'     => 'atd_cfi_special_id',
				'compare' => 'EXISTS'
			]
		] ) ) {
			// remove metadata
			delete_post_meta( $originalPost->ID, Feed\SpecialDeparture::$metaKeyId );
			delete_post_meta( $originalPost->ID, 'atd_cfi_special_id' );
			delete_post_meta( $originalPost->ID, 'atd_cfi_valid_from' );
			delete_post_meta( $originalPost->ID, 'atd_cfi_valid_to' );

			// remove terms
			if ( $terms = wp_get_post_terms( $originalPost->ID, Taxonomy\SpecialType::$name ) ) {
				wp_remove_object_terms( $originalPost->ID, array_column( $terms, 'term_id' ), Taxonomy\SpecialType::$name );
			}
			if ( $terms = wp_get_post_terms( $originalPost->ID, Taxonomy\PromoCode::$name ) ) {
				wp_remove_object_terms( $originalPost->ID, array_column( $terms, 'term_id' ), Taxonomy\PromoCode::$name );
			}

			$post_details = [
				'ID'          => $originalPost->ID,
				'post_title'  => $details->getSailingdate()->getCruise()->getName() . ' - ' . $details->getSailingDate()->getSailingDate()->format( 'd/m/Y' ),
				'post_name'   => sanitize_title( $details->getSailingdate()->getCruise()->getName() . ' - ' . $details->getSailingDate()->getSailingDate()->format( 'd/m/Y' ) ),
				'post_status' => 'publish',
				'meta_input'  => [
					Feed\Departure::$metaKeyId => $details->getSailingdate()->getId(),
					'atd_cfi_cruise_id'        => $details->getSailingdate()->getCruise()->getId(),
					'atd_cfi_sailing_date'     => $details->getSailingdate()->getSailingdate()->getTimestamp()
				]
			];

			wp_update_post( $post_details );

			Logger::modify( sprintf( '[%d] Converted special %s post %s to cruise %s', $post_details['meta_input']['atd_cfi_id'], $originalPost->post_type, $originalPost->post_title, $post_details['post_title'] ) );

			return true;
		}

		return false;
	}

	public static function register() {
		register_post_type( self::$postType, [
			'labels'              => [
				'name'               => 'Departures',
				'singular_name'      => 'Departure',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Departure',
				'edit_item'          => 'Edit Departure',
				'new_item'           => 'New Departure',
				'view_item'          => 'View Departure',
				'search_items'       => 'Search Departures',
				'not_found'          => 'No Departures found',
				'not_found_in_trash' => 'No Departures found in trash',
				'parent_item_colon'  => 'Parent Departures:',
			],
			'hierarchical'        => false,
			'description'         => 'Departure Posts',
			'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
			'taxonomies'          => [
				Taxonomy\Destination::$name,
				Taxonomy\CruiseLine::$name,
				Taxonomy\Ship::$name,
				Taxonomy\Duration::$name,
				Taxonomy\EmbarkPort::$name,
				Taxonomy\DisembarkPort::$name,
				Taxonomy\Month::$name,
				Taxonomy\DepartureType::$name,
				Taxonomy\SpecialType::$name
			],
			'show_ui'             => true,
			'show_in_menu'        => ATD_CF_XML_MENU_SLUG,
			'show_in_rest'        => true,
			'has_archive'         => 'cruise-search',
			'menu_position'       => 30,
			'menu_icon'           => 'dashicons-media-text',
			'exclude_from_search' => false,
			'public'              => true,
			'capability_type'     => 'post',
		] );
	}
}