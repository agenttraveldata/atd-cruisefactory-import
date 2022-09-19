<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use ATD\CruiseFactory\Services\WordPress\Blocks\Paragraph;
use WP_Query;

class Ship implements Post {
	public static string $postType = 'atd_cf_ship';
	private static string $cfImageUrl = 'https://ik.imagekit.io/atd/ships/thumbnails/';

	public static function add( Entity\Ship $details ): ?int {
		$originalPost = new WP_Query( [
			'post_type'      => self::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_key'       => Feed\Ship::$metaKeyId,
			'meta_value'     => $details->getId()
		] );

		if ( ! defined( 'ATD_CF_XML_IMPORT_FORCE_OVERWRITE' ) ) {
			if ( $originalPost->post_count === 1 && $originalPost->post->post_author > 0 ) {
				return $originalPost->post->ID;
			}
		}

		$post_details = [
			'post_type'    => self::$postType,
			'post_title'   => $details->getName(),
			'post_content' => ( new Paragraph( nl2br( trim( $details->getDescription() ) ) ) )->render(),
			'post_excerpt' => substr( $details->getDescription(), 0, 200 ) . ( strlen( $details->getDescription() ) > 200 ? '...' : '' ),
			'post_status'  => 'publish',
			'meta_input'   => [
				Feed\Ship::$metaKeyId => $details->getId()
			]
		];

		if ( $originalPost->post_count === 1 ) {
			$post_details['ID'] = $originalPost->post->ID;
			Logger::modify( sprintf( '[%d] Updated %s post %s', $post_details['meta_input'][ Feed\Ship::$metaKeyId ], $post_details['post_type'], $originalPost->post->post_title ) );
		} else {
			Logger::add( sprintf( '[%d] Added %s post %s', $post_details['meta_input'][ Feed\Ship::$metaKeyId ], $post_details['post_type'], $post_details['post_title'] ) );
		}

		$post_id = wp_insert_post( $post_details );

		if ( ! defined( 'ATD_CF_XML_IMAGE_EXCLUDE' ) && ! empty( $details->getThumbnail() ) ) {
			$imageUrl      = self::$cfImageUrl . $details->getThumbnail();
			$imageFileName = 'atd-cfi_' . wp_basename( $imageUrl ) . ( pathinfo( $imageUrl, PATHINFO_EXTENSION ) === '' ? '.jpg' : '' );

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

			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$file = [
				'name'     => $imageFileName,
				'tmp_name' => download_url( $imageUrl )
			];

			if ( ! is_wp_error( $file['tmp_name'] ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );

				$id = media_handle_sideload( $file, $post_id, "Image for ship {$details->getName()}" );

				if ( is_wp_error( $id ) ) {
					@unlink( $file['tmp_name'] );
				} else {
					set_post_thumbnail( $post_id, $id );
				}
			}
		}

		return true;
	}

	public static function register() {
		register_post_type( self::$postType, [
			'labels'              => [
				'name'               => 'Ships',
				'singular_name'      => 'Ship',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Ship',
				'edit_item'          => 'Edit Ship',
				'new_item'           => 'New Ship',
				'view_item'          => 'View Ship',
				'search_items'       => 'Search Ships',
				'not_found'          => 'No Ships found',
				'not_found_in_trash' => 'No Ships found in trash',
				'parent_item_colon'  => 'Parent Ships:',
			],
			'hierarchical'        => false,
			'description'         => 'Ship Posts',
			'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
			'taxonomies'          => [
				'category',
				'post_tag'
			],
			'show_ui'             => true,
			'show_in_menu'        => ATD_CF_XML_MENU_SLUG,
			'show_in_rest'        => true,
			'has_archive'         => 'ships',
			'rewrite'             => [
				'slug' => 'ship'
			],
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-media-text',
			'exclude_from_search' => false,
			'public'              => true,
			'capability_type'     => 'post',
		] );
	}
}