<?php


namespace ATD\CruiseFactory\Services\WordPress\Templates;


class Provider {
	private static bool $themeSupport = false;

	public static function register() {
		self::$themeSupport = true;//current_theme_supports( 'cruisefactory' );

		if ( self::$themeSupport ) {
			add_filter( 'template_include', [ new Loader(), 'templateLoader' ] );
		}
	}
}