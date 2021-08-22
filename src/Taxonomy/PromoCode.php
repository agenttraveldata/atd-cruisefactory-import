<?php


namespace ATD\CruiseFactory\Taxonomy;


class PromoCode extends AbstractTaxonomy {
	public static string $name = 'atd_cf_promo_code';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Promo Code' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true,
			'rewrite'      => [ 'slug' => 'cruise-search/promo' ]
		] );
	}
}