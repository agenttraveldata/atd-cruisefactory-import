<?php

namespace ATD\CruiseFactory\Services\WordPress;

use ATD\CruiseFactory\Feed;

class Search {
	public static function parseQuery( \WP_Query $query ): void {
		if ( $query->is_search() ) {
			if ( ! preg_match( '/d:([0-9]+)/', $query->query['s'], $m ) ) {
				return;
			}

			// remove from search query string
			$query->set( 's', str_replace( $m[0], '', $query->query['s'] ) );

			$metaQuery   = $query->get( 'meta_query', [] );
			$metaQuery[] = [
				'relation'                        => 'OR',
				'atd_search_by_departure'         => [
					'key'     => Feed\Departure::$metaKeyId,
					'value'   => $m[1],
					'type'    => 'NUMERIC',
					'compare' => '='
				],
				'atd_search_by_special_departure' => [
					'key'     => Feed\SpecialDeparture::$metaKeyId,
					'value'   => $m[1],
					'type'    => 'NUMERIC',
					'compare' => '='
				]
			];

			$query->set( 'meta_query', $metaQuery );
		}
	}
}