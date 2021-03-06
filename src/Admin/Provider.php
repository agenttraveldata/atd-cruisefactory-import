<?php


namespace ATD\CruiseFactory\Admin;


use ATD\CruiseFactory\Feed\Factory;
use ATD\CruiseFactory\Services\WordPress\Commands\Import;

class Provider {
	public static function registerAdminPages() {
		add_action( 'admin_menu', [ Overview::class, 'register' ] );
	}

	public static function registerAdminAjaxCalls() {
		add_action( 'wp_ajax_atd_cfi_verify_xml', function () {
			if ( false !== check_ajax_referer( 'atd_cfi_verify_xml', 'verify_xml' ) ) {
				define( 'ATD_CF_XML_IMPORT_NO_CACHE', true );
				update_option( ATD_CF_XML_KEY_FIELD, $_POST['key'] );

				if ( $updatedAt = ( new Factory() )->fetchServices() ) {
					/* Successfully added factory details, we can assume XML key worked */
					update_option( ATD_CF_XML_VERIFIED_FIELD, true );
					wp_send_json_success( [ 'services' => $updatedAt ] );
				}

				update_option( ATD_CF_XML_VERIFIED_FIELD, false );
				wp_send_json_error( [ 'services' => false ] );
			}
		} );

		add_action( 'wp_ajax_atd_cfi_save_recaptcha_keys', function () {
			if ( false !== check_ajax_referer( 'atd_cfi_save_recaptcha_keys', 'save_recaptcha' ) ) {
				update_option( ATD_CF_XML_GOOGLE_TYPE_FIELD, $_POST[ ATD_CF_XML_GOOGLE_TYPE_FIELD ] ?? '' );
				update_option( ATD_CF_XML_GOOGLE_SITE_KEY_FIELD, $_POST[ ATD_CF_XML_GOOGLE_SITE_KEY_FIELD ] ?? '' );
				update_option( ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD, $_POST[ ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD ] ?? '' );
				wp_send_json_success( [ 'message' => 'Successfully updated keys.' ] );
			}

			wp_send_json_error( [ 'message' => 'Failed to update keys.' ] );
		} );

		add_action( 'wp_ajax_atd_cfi_get_feeds', function () {
			if ( false !== check_ajax_referer( 'atd_cfi_get_feeds', 'get_feeds' ) ) {
				wp_send_json_success( [ 'feeds' => \ATD\CruiseFactory\Feed\Provider::getPublicFeeds() ] );
			}

			wp_send_json_error( [ 'feeds' => false ] );
		} );

		add_action( 'wp_ajax_atd_cfi_import_xml', function () {
			if ( false !== check_ajax_referer( 'atd_cfi_import_xml', 'xml_import' ) ) {
				$command = new Import();
				$command->increment( [ 'departures', 'special-departures', 'destinations', 'cruise-lines', 'ships' ], [
					'wordpress' => 'import',
					'images'    => 'exclude',
					'cache'     => 'cache'
				] );

				wp_send_json_success( [ 'success' => true ] );
			}

			wp_send_json_error( [ 'success' => false ] );
		} );
	}
}