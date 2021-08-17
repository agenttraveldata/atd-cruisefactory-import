<?php


namespace ATD\CruiseFactory\Taxonomy;


class Ship extends AbstractTaxonomy {
	public static string $name = 'atd_cf_ship';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'            => [ 'name' => 'Ship' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}