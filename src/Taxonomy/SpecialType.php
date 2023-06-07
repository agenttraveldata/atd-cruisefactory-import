<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class SpecialType extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_special_type';

	public static function register(): void {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'       => [ 'name' => 'Special Type' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/special' ]
		] );
	}
}