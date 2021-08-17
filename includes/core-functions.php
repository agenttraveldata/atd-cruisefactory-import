<?php

use ATD\CruiseFactory\Services\WordPress\Posts\Finder;

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

/**
 * @param string $slug
 * @param string|null $name
 * @param array $args
 * @param bool $load
 *
 * @return string
 */
function atd_cf_get_template_part( string $slug, ?string $name = null, array $args = [], bool $load = true ): string {
	do_action( 'get_template_part_' . $slug, $slug, $name, $args );

	$templates = [];
	foreach ( [ 'parts/', '' ] as $prefix ) {
		if ( isset( $name ) ) {
			$templates[] = $prefix . $slug . '-' . $name . '.php';
		}
		$templates[] = $prefix . $slug . '.php';
	}

	$templates = apply_filters( 'atd_cfi_get_template_part', $templates, $slug, $name, $args );

	return atd_cfi_locate_template( $templates, $args, $load, false );
}

/**
 * @param $template_names
 * @param array $args
 * @param false $load
 * @param bool $require_once
 *
 * @return string
 */
function atd_cfi_locate_template( $template_names, array $args = [], bool $load = false, bool $require_once = true ): string {
	$located = '';

	if ( ! is_array( $template_names ) ) {
		$template_names = [ $template_names ];
	}

	foreach ( $template_names as $template_name ) {
		if ( empty( $template_name ) ) {
			continue;
		}

		$template_name = ltrim( $template_name, '/' );

		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'cruisefactory/' . $template_name ) ) {
			$located = trailingslashit( get_stylesheet_directory() ) . 'cruisefactory/' . $template_name;
			break;
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'cruisefactory/' . $template_name ) ) {
			$located = trailingslashit( get_template_directory() ) . 'cruisefactory/' . $template_name;
			break;
		} elseif ( file_exists( trailingslashit( plugin_dir_path( ATD_CF_PLUGIN_FILE ) ) . 'templates/' . $template_name ) ) {
			$located = trailingslashit( plugin_dir_path( ATD_CF_PLUGIN_FILE ) ) . 'templates/' . $template_name;
			break;
		}
	}

	if ( $load && ! empty( $located ) ) {
		load_template( $located, $require_once, $args );
	}

	return $located;
}

/**
 * @param string $postType
 * @param int $metaValue
 * @param bool $returnQuery
 *
 * @return false|WP_Post|WP_Query|null
 */
function atd_cf_get_post_by_meta_value( string $postType, int $metaValue, bool $returnQuery = false ) {
	if ( $post = Finder::getPostByPostTypeAndId( $postType, $metaValue, $returnQuery ) ) {
		return $post;
	}

	return false;
}

function atd_cf_get_query_for_posts_by_meta( string $postType, array $metaValues ): WP_Query {
	return Finder::getQueryByPostTypeAndMetaValues( $postType, $metaValues );
}

/**
 * @param string $postType
 * @param int $metaValue
 *
 * @return false|string|WP_Error
 */
function atd_cf_get_permalink_by_meta_value( string $postType, int $metaValue ) {
	if ( $post = Finder::getPostByPostTypeAndId( $postType, $metaValue ) ) {
		return get_permalink( $post->ID );
	}

	return '';
}

/**
 * @param int|null $post_id
 *
 * @return array
 */
function atd_cf_get_post_attached_images( ?int $post_id = null, ?string $image_type = null ): array {
	return Finder::getPostAttachments( $post_id, $image_type );
}