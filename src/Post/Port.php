<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use ATD\CruiseFactory\Services\WordPress\Blocks\Paragraph;
use WP_Query;

class Port implements Post {
	public static string $postType = 'atd_cf_port';

	public static function add( Entity\Port $details ): ?int {
		$originalPost = new WP_Query( [
			'post_type'      => self::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_key'       => Feed\Port::$metaKeyId,
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
			'post_author'  => 0,
			'post_content' => ( new Paragraph( nl2br( trim( $details->getDescription() ) ) ) )->render(),
			'post_excerpt' => substr( $details->getDescription(), 0, 200 ) . ( strlen( $details->getDescription() ) > 200 ? '...' : '' ),
			'post_status'  => 'publish',
			'meta_input'   => [
				Feed\Port::$metaKeyId => $details->getId()
			]
		];

		if ( $details->getLatLong() ) {
			$post_details['meta_input']['atd_cfi_lat']  = $details->getLatLong()->getLat();
			$post_details['meta_input']['atd_cfi_long'] = $details->getLatLong()->getLong();
		}

		if ( $originalPost->post_count === 1 ) {
			$post_details['ID'] = $originalPost->post->ID;
			Logger::modify( "[{$post_details['meta_input'][ Feed\Port::$metaKeyId ]}] Updated {$post_details['post_type']} post {$originalPost->post->post_title}" );
		} else {
			Logger::add( "[{$post_details['meta_input'][ Feed\Port::$metaKeyId ]}] Added {$post_details['post_type']} post {$post_details['post_title']}" );
		}

		$post_id = wp_insert_post( $post_details );

		if ( is_wp_error( $post_id ) ) {
			Logger::error( "[{$details->getId()}] Port error: {$post_id->get_error_message()}" );

			return null;
		}

		return $post_id;
	}

	public static function register() {
		register_post_type( self::$postType, [
			'labels'              => [
				'name'               => 'Ports',
				'singular_name'      => 'Port',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Port',
				'edit_item'          => 'Edit Port',
				'new_item'           => 'New Port',
				'view_item'          => 'View Port',
				'search_items'       => 'Search Ports',
				'not_found'          => 'No Ports found',
				'not_found_in_trash' => 'No Ports found in trash',
				'parent_item_colon'  => 'Parent Ports:',
			],
			'hierarchical'        => false,
			'description'         => 'Port Posts',
			'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
			'taxonomies'          => [
				'category',
				'post_tag'
			],
			'show_ui'             => true,
			'show_in_menu'        => ATD_CF_XML_MENU_SLUG,
			'show_in_rest'        => true,
			'has_archive'         => 'ports',
			'rewrite'             => [
				'slug' => 'port'
			],
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-media-text',
			'exclude_from_search' => false,
			'public'              => true,
			'capability_type'     => 'post',
		] );
	}
}