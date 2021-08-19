<?php


namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;


class EnquiryForm {
	public function shortcode(): string {
		ob_start();
		include atd_cf_get_template_part( 'form', 'enquiry', [], false );

		return ob_get_clean();
	}
}