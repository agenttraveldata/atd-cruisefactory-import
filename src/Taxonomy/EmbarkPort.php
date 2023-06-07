<?php


namespace ATD\CruiseFactory\Taxonomy;


use ATD\CruiseFactory\Post\Departure;

class EmbarkPort extends AbstractTaxonomy {
	public static string $name = 'atd_cf_tax_departure_port';

	public static function register(): void {
		register_taxonomy( self::$name, [ Departure::$postType ], [
			'labels'       => [ 'name' => 'Embark Port' ],
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => true
		] );
	}
}