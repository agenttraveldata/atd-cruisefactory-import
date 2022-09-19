<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class CruiseLine extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_cruise_line';

	public static function register() {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'            => [ 'name' => 'Cruise Line' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}