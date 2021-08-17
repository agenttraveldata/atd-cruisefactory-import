<?php


namespace ATD\CruiseFactory\Taxonomy;


class EmbarkPort extends AbstractTaxonomy {
	public static string $name = 'atd_cf_departure_port';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'       => [ 'name' => 'Embark Port' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true
		] );
	}
}