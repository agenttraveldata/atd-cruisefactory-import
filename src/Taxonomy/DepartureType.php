<?php


namespace ATD\CruiseFactory\Taxonomy;


class DepartureType extends AbstractTaxonomy {
	public static string $name = 'atd_cf_departure_type';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Departure Type' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/type' ]
		] );
	}
}