<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class DisembarkPort extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_arrival_port';

	public static function register() {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'       => [ 'name' => 'Disembark Port' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true
		] );
	}
}