<?php


namespace ATD\CruiseFactory\Services\WordPress\Commands;


use Exception;
use WP_CLI;

class Provider {
	public static function registerCommands() {
		try {
			WP_CLI::add_command( 'atd import', Import::class );
		} catch ( Exception $e ) {
		}
	}
}