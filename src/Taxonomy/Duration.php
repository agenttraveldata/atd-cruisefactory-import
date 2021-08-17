<?php


namespace ATD\CruiseFactory\Taxonomy;


class Duration extends AbstractTaxonomy {
	public static string $name = 'atd_cf_duration';

	public static function register() {
		register_taxonomy( self::$name, [ 'departure' ], [
			'labels'            => [ 'name' => 'Duration' ],
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true
		] );
	}
}