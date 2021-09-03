<?php


namespace ATD\CruiseFactory\Services\WordPress\Shortcodes;


class Provider {
	public static function register() {
		add_shortcode( 'atd-cfi-search-form', [ ( new SearchForm() ), 'shortcode' ] );
		add_shortcode( 'atd-cfi-enquiry-form', [ ( new EnquiryForm() ), 'shortcode' ] );
		add_shortcode( 'atd-cfi-departure-summary', [ ( new EnquirySummary() ), 'shortcode' ] );
	}
}