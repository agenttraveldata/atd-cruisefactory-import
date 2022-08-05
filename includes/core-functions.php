<?php

use ATD\CruiseFactory\Entity;
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

/**
 * @param string $postType
 * @param array $metaValues
 *
 * @return WP_Query
 */
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
 * @param string|null $image_type
 *
 * @return array
 */
function atd_cf_get_post_attached_images( ?int $post_id = null, ?string $image_type = null ): array {
	return Finder::getPostAttachments( $post_id, $image_type );
}

/**
 * @param int $departure_id
 * @param string $departure_type
 *
 * @return Entity\DepartureSummary
 */
function atd_cf_get_departure_details( int $departure_id, string $departure_type ): Entity\DepartureSummary {
	try {
		$departure = Finder::getDepartureByIdAndType( $departure_id, $departure_type );
	} catch ( Exception $e ) {
		wp_redirect( '/' );
		exit;
	}

	$summary = new Entity\DepartureSummary();
	$summary->setId( $departure_id );
	$summary->setType( $departure_type );

	switch ( $departure_type ) {
		case 'special':
			/** @var Entity\SpecialDeparture $departure */
			$summary->setSpecial( $departure->getSpecial() );
			$summary->setCruise( $departure->getSailingdate()->getCruise() );
			$summary->setSailingDate( $departure->getSailingdate()->getSailingDate() );
			if ( $departure->getSpecial()->getSpecialLeadPrice() ) {
				$summary->setSpecialLeadPrice( $departure->getSpecial()->getSpecialLeadPrice() );
			}
			$specialPrice = $departure->getSpecial()->getSpecialPrices()->filter( function ( $p ) {
				/** @var Entity\SpecialPrice $p */
				return $p->getId() === (int) get_query_var( 'cabin_price' );
			} );
			$summary->setSpecialPrice( $specialPrice->count() === 1 ? $specialPrice->first() : null );
			break;
		case 'cruise':
			/** @var Entity\Departure $departure */
			$summary->setCruise( $departure->getCruise() );
			$summary->setSailingDate( $departure->getSailingDate() );
			$cruisePrice = $departure->getCruisePrices()->filter( function ( $p ) {
				/** @var Entity\CruisePrice $p */
				return $p->getId() === (int) get_query_var( 'cabin_price' );
			} );
			$summary->setCruisePrice( $cruisePrice->count() === 1 ? $cruisePrice->first() : null );
			break;
	}

	return $summary;
}

/**
 * @param string $key
 * @param int $id
 * @return string|null
 */
function atd_cf_get_media_image_by_meta_key_and_id( string $key, int $id ): ?string {
    $q = new WP_Query( [
        'nopaging'               => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
        'posts_per_page'         => 1,
        'post_type'              => 'attachment',
        'post_status'            => 'inherit',
        'meta_query'             => [
            [
                'key'     => $key,
                'value'   => $id,
                'compare' => '='
            ]
        ]
    ] );

    if ( ! empty( $q->post ) ) {
        if ( false !== $image = wp_get_attachment_image_url( $q->post->ID, 'full' ) ) {
            return $image;
        }
    }

    return null;
}