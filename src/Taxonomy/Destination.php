<?php


namespace ATD\CruiseFactory\Taxonomy;


class Destination extends AbstractTaxonomy {
	public static string $name = 'atd_cf_destination';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'            => [ 'name' => 'Destination' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}