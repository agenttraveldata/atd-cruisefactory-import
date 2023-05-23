<?php

namespace ATD\CruiseFactory\Services\WordPress\Plugins\ContactForm7;

class Setup {
	public function __construct() {
		/**
		 * unfortunately this must be done, apologies if there's people out there that need this!
		 * it can't be turned off only for one form
		 */
		add_filter( 'wpcf7_autop_or_not', '__return_false' );
		add_action( 'wpcf7_init', [ $this, 'addHandlers' ] );
	}

	public function addHandlers(): void {
		$enquiryHandler = new EnquiryHandler();

		add_filter( 'wpcf7_posted_data', [ $enquiryHandler, 'hydrateFormData' ] );
		wpcf7_add_form_tag( 'atd_cfi_enquiry', [ $enquiryHandler, 'insertHiddenFields' ] );
	}
}