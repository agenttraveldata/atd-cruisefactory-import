<?php


namespace ATD\CruiseFactory\Taxonomy;


abstract class AbstractTaxonomy {
	public static function addTerm( string $taxonomy, string $taxonomy_term, ?string $slug = null ): ?array {
		$taxonomy_term = htmlspecialchars( $taxonomy_term );
		if ( $slug && $term = get_term_by( 'slug', $slug, $taxonomy, ARRAY_A ) ) {
			$term = wp_update_term( $term['term_id'], $taxonomy, [ 'name' => $taxonomy_term ] );
		} else if ( ! $term = term_exists( $taxonomy_term, $taxonomy ) ) {
			$term = wp_insert_term( $taxonomy_term, $taxonomy, [ 'slug' => sanitize_title( $slug ?? $taxonomy_term ) ] );
		}

		if ( ! is_array( $term ) ) {
			return null;
		}

		return $term;
	}
}