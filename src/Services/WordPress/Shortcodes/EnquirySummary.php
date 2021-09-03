<?php

namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;

class EnquirySummary {
	public function shortcode(): string {
		ob_start();
		include atd_cf_get_template_part( 'content/departure', 'enquiry-summary', [], false );

		return ob_get_clean();
	}
}