<?php


namespace ATD\CruiseFactory\Taxonomy;


class CruiseType extends AbstractTaxonomy {
	public static string $name = 'atd_cf_cruise_type';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Cruise Type' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/style' ]
		] );
	}
}