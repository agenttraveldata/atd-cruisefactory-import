<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class PromoCode extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_promo_code';

	public static function register(): void {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'       => [ 'name' => 'Promo Code' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/promo' ]
		] );
	}
}