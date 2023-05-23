<?php

namespace ATD\CruiseFactory\Services\WordPress\Plugins;

use ATD\CruiseFactory\Services\WordPress\Plugins\ContactForm7\Setup;

class Provider {
	public static function registerPluginExtensions(): void {
		if ( in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			(new Setup());
		}
	}
}