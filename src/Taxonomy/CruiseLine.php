<?php


namespace ATD\CruiseFactory\Taxonomy;


class CruiseLine extends AbstractTaxonomy {
	public static string $name = 'atd_cf_cruise_line';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'            => [ 'name' => 'Cruise Line' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}