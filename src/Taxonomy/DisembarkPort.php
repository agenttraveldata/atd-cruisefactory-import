<?php


namespace ATD\CruiseFactory\Taxonomy;


class DisembarkPort extends AbstractTaxonomy {
	public static string $name = 'atd_cf_arrival_port';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Disembark Port' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true
		] );
	}
}