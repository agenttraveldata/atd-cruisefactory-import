<?php


namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;


class SearchForm {
	public function shortcode(): string {
		ob_start();
		include atd_cf_get_template_part( 'form', 'search', [], false );

		return ob_get_clean();
	}
}