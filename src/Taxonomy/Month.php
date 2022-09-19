<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class Month extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_month';

	public static function register() {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'            => [ 'name' => 'Month' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}