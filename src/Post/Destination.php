<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use ATD\CruiseFactory\Services\WordPress\Blocks\Paragraph;
use WP_Query;

class Destination implements Post {
	public static string $postType = 'destination';
	private static string $cfImageUrl = 'https://ik.imagekit.io/atd/destinations/image/';

	public static function add( Entity\Destination $details ): ?int {
		$originalPost = new WP_Query( [
			'post_type'      => self::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_key'       => Feed\Destination::$metaKeyId,
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
			'post_excerpt' => $details->getBriefDescription(),
			'post_status'  => 'publish',
			'meta_input'   => [
				Feed\Destination::$metaKeyId => $details->getId()
			]
		];

		if ( $originalPost->post_count === 1 ) {
			$post_details['ID'] = $originalPost->post->ID;
			Logger::modify( sprintf( '[%d] Updated %s post %s', $post_details['meta_input'][ Feed\Destination::$metaKeyId ], $post_details['post_type'], $originalPost->post->post_title ) );
		} else {
			Logger::add( sprintf( '[%d] Added %s post %s', $post_details['meta_input'][ Feed\Destination::$metaKeyId ], $post_details['post_type'], $post_details['post_title'] ) );
		}

		$post_id = wp_insert_post( $post_details );

		if ( ! defined( 'ATD_CF_XML_IMAGE_EXCLUDE' ) && ! empty( $details->image ) ) {
			$imageUrl       = self::$cfImageUrl . $details->image;
			$imageExtension = strtolower( pathinfo( $imageUrl, PATHINFO_EXTENSION ) );
			$imageFileName  = 'atd-cfi_destination-' . $details->getId() . ( $imageExtension === '' ? '.jpg' : '.' . $imageExtension );

			$attachmentMetaKey = 'atd_cfi_destination_id';
			$attachment        = new WP_Query( [
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'nopaging'       => true,
				'no_found_rows'  => true,
				'meta_query'     => [
					'relation' => 'AND',
					'id'       => [
						'key'   => $attachmentMetaKey,
						'value' => $details->getId()
					]
				]
			] );

			if ( $attachment->post_count === 1 ) {
				if ( defined( 'ATD_CF_XML_IMAGE_OVERWRITE' ) ) {
					$thumb     = wp_get_attachment_image_url( $attachment->post->ID );
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

				$attachmentPostData = [
					'post_title'  => 'Image for destination ' . $details->getName(),
					'post_status' => 'publish',
					'meta_input'  => [
						$attachmentMetaKey => $details->getId()
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

		return true;
	}

	public static function register() {
		register_post_type( self::$postType, [
			'labels'              => [
				'name'               => 'Destinations',
				'singular_name'      => 'Destination',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Destination',
				'edit_item'          => 'Edit Destination',
				'new_item'           => 'New Destination',
				'view_item'          => 'View Destination',
				'search_items'       => 'Search Destinations',
				'not_found'          => 'No Destinations found',
				'not_found_in_trash' => 'No Destinations found in trash',
				'parent_item_colon'  => 'Parent Destinations:',
			],
			'hierarchical'        => false,
			'description'         => 'Destination Posts',
			'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
			'taxonomies'          => [
				'category',
				'post_tag'
			],
			'show_ui'             => true,
			'show_in_menu'        => ATD_CF_XML_MENU_SLUG,
			'show_in_rest'        => true,
			'has_archive'         => 'destinations',
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-media-text',
			'exclude_from_search' => false,
			'public'              => true,
			'capability_type'     => 'post',
		] );
	}
}