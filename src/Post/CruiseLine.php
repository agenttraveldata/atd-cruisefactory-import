<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use ATD\CruiseFactory\Services\WordPress\Blocks\Paragraph;
use WP_Query;

class CruiseLine implements Post {
	public static string $postType = 'cruise-line';

	public static function add( Entity\CruiseLine $details ): ?int {
		$originalPost = new WP_Query( [
			'post_type'      => self::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_key'       => Feed\CruiseLine::$metaKeyId,
			'meta_value'     => $details->id
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
				Feed\CruiseLine::$metaKeyId => $details->getId()
			]
		];

		if ( $originalPost->post_count === 1 ) {
			$post_details['ID'] = $originalPost->post->ID;
			Logger::modify( sprintf( '[%d] Updated %s post %s', $post_details['meta_input'][ Feed\CruiseLine::$metaKeyId ], $post_details['post_type'], $originalPost->post->post_title ) );
		} else {
			Logger::add( sprintf( '[%d] Added %s post %s', $post_details['meta_input'][ Feed\CruiseLine::$metaKeyId ], $post_details['post_type'], $post_details['post_title'] ) );
		}

		$post_id = wp_insert_post( $post_details );

		if ( ! empty( $details->getLogoData() ) ) {
			$logoFileName = 'atd-cfi_cruise-line-logo_' . $details->id . '.' . self::getExtension( $details->getLogoType() );

			$tfp = tmpfile();
			fwrite( $tfp, $details->getLogoData() );
			$imageFile = stream_get_meta_data( $tfp )['uri'];
			fseek( $tfp, 0 );

			if ( has_post_thumbnail( $post_id ) ) {
				if ( defined( 'ATD_CF_XML_IMAGE_OVERWRITE' ) ) {
					$thumb     = get_the_post_thumbnail_url( $post_id );
					$uploadDir = wp_get_upload_dir();
					$subPath   = str_replace( $uploadDir['baseurl'], '', $thumb );
					$thumbPath = $uploadDir['basedir'] . $subPath;
					file_put_contents( $thumbPath, file_get_contents( $imageFile ) );
				}

				fclose( $tfp );

				return true;
			}

			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$file = [
				'name'     => $logoFileName,
				'tmp_name' => $imageFile
			];

			if ( ! is_wp_error( $file['tmp_name'] ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );

				$id = media_handle_sideload( $file, $post_id, "Image for cruise line {$details->getName()}" );

				if ( ! is_wp_error( $id ) ) {
					set_post_thumbnail( $post_id, $id );
				}
			}

			fclose( $tfp );
		}

		return true;
	}

	private static function getExtension( string $mimeType ): string {
		switch ( $mimeType ) {
			case 'image/gif':
				return 'gif';
			case 'image/png':
				return 'png';
			default:
				return 'jpg';
		}
	}

	public static function register() {
		register_post_type( self::$postType, [
			'labels'              => [
				'name'               => 'Cruise Lines',
				'singular_name'      => 'Cruise Line',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Cruise Line',
				'edit_item'          => 'Edit Cruise Line',
				'new_item'           => 'New Cruise Line',
				'view_item'          => 'View Cruise Line',
				'search_items'       => 'Search Cruise Lines',
				'not_found'          => 'No Cruise Lines found',
				'not_found_in_trash' => 'No Cruise Lines found in trash',
				'parent_item_colon'  => 'Parent Cruise Lines:',
			],
			'hierarchical'        => false,
			'description'         => 'Cruise Line Posts',
			'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
			'taxonomies'          => [
				'category',
				'post_tag'
			],
			'show_ui'             => true,
			'show_in_menu'        => ATD_CF_XML_MENU_SLUG,
			'show_in_rest'        => true,
			'has_archive'         => 'cruise-lines',
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-media-text',
			'exclude_from_search' => false,
			'public'              => true,
			'capability_type'     => 'post',
		] );
	}
}