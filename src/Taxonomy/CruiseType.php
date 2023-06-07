<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class CruiseType extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_cruise_type';

	public static function register(): void {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'       => [ 'name' => 'Cruise Type' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/style' ]
		] );
	}
}