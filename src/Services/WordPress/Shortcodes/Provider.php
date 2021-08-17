<?php


namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;


class Provider {
	public static function register() {
		add_shortcode('atd_cfi_search_form', [(new Search()), 'shortcode']);
	}
}