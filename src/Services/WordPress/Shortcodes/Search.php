<?php


namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;


class Search {
	public function shortcode(): string {
		ob_start();
		include atd_cf_get_template_part( 'search', 'form', [], false );

		return ob_get_clean();
	}
}