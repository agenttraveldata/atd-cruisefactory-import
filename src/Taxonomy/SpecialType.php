<?php


namespace ATD\CruiseFactory\Taxonomy;


class SpecialType extends AbstractTaxonomy {
	public static string $name = 'atd_cf_special_type';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Special Type' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/type/special/type' ]
		] );
	}
}