<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Services\Form\Validators\EnquiryValidator;
use ATD\CruiseFactory\Services\Google\ReCaptcha;
use WP_REST_Request;
use WP_REST_Server;

class EnquireController extends AbstractController {
	protected string $apiEndpointPrefix = '/enquire';

	public function __construct() {
		$this->addRoute( '', [ $this, 'send' ], false, WP_REST_Server::CREATABLE );
	}

	public function send( WP_REST_Request $request ): void {
		set_query_var( 'departure_id', (int) $request->get_param( 'departure_id' ) );
		set_query_var( 'departure_type', $request->get_param( 'departure_type' ) );
		set_query_var( 'pax', in_array( $request->get_param( 'pax' ), ATD_CF_XML_PAX_TYPES ) ? $request->get_param( 'pax' ) : null );
		set_query_var( 'request_cabin', (int) $request->get_param( 'request_cabin' ) );
		set_query_var( 'lead_price', in_array( $request->get_param( 'lead_price' ), ATD_CF_XML_LEAD_CATEGORIES ) ? ucfirst( $request->get_param( 'lead_price' ) ) : null );
		set_query_var( 'cabin_price', (int) $request->get_param( 'cabin_price' ) );

		if ( $summary = atd_cf_get_departure_details( get_query_var( 'departure_id' ), get_query_var( 'departure_type' ) ) ) {
			$validator = new EnquiryValidator();

			if ( $validator->validate() ) {
				if ( $secretKey = get_option( ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD ) ) {
					$reCaptcha = new ReCaptcha( $secretKey );
					if ( ! $reCaptcha->validate( $_POST['g-recaptcha-response'] ) ) {
						wp_send_json_error( [ 'message' => 'Google reCaptcha failed.' ] );
					}
				}

				$fields = $validator->getSanitizedFields();

				$fields['pax']           = get_query_var( 'pax' );
				$fields['request_cabin'] = get_query_var( 'request_cabin' );
				$fields['lead_price']    = get_query_var( 'lead_price' );
				$fields['cabin_price']   = get_query_var( 'cabin_price' );
				$fields['summary']       = $summary;

				add_filter( 'wp_mail_from', [ $this, 'mailFrom' ], 10, 0 );
				add_filter( 'wp_mail_from_name', [ $this, 'mailFromName' ], 10, 0 );
				add_filter( 'wp_mail_content_type', [ $this, 'mailContentType' ], 10, 0 );

				$to      = get_option( ATD_CF_XML_AGENT_EMAIL_FIELD, get_option( 'admin_email' ) );
				$bcc     = get_option( ATD_CF_XML_BCC_EMAIL_FIELD, null );
				$subject = sprintf( '%s enquiry sent from %s website', $summary->getSpecial() ? 'Special' : 'Cruise', get_bloginfo( 'name' ) );

				if ( $this->dispatchEmail( 'agent', $fields, $to, $subject, $bcc ) ) {
					$to      = $fields['email_address'];
					$subject = sprintf( 'Thank you for your enquiry on %s', get_bloginfo( 'name' ) );

					$this->dispatchEmail( 'client', $fields, $to, $subject );
				}

				remove_filter( 'wp_mail_from', [ $this, 'mailFrom' ] );
				remove_filter( 'wp_mail_from_name', [ $this, 'mailFromName' ] );
				remove_filter( 'wp_mail_content_type', [ $this, 'mailContentType' ] );

				wp_send_json_success( [ 'message' => 'Thank you for your enquiry.' ] );
			}

			wp_send_json_error( [ 'message' => 'Your message has the following issues:<ul>' . $validator->getMessages() . '</ul>' ] );
		}

		wp_send_json_error( [ 'message' => 'The departure you are enquiring for is no longer valid.' ] );
	}

	private function dispatchEmail( string $templatePart, array $fields, string $to, string $subject, ?string $bcc = null ): bool {
		ob_start();
		atd_cf_get_template_part( 'emails/enquiry', $templatePart, $fields );
		$body    = ob_get_clean();
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		if ( ! empty( $bcc ) ) {
			$headers[] = 'Bcc: ' . $bcc;
		}

		if ( ! empty( $body ) ) {
			return wp_mail( $to, $subject, $body, $headers );
		}

		return false;
	}

	public function mailFrom(): string {
		return get_option( ATD_CF_XML_SEND_FROM_EMAIL_FIELD, get_option( 'admin_email' ) );
	}

	public function mailFromName(): string {
		return get_bloginfo( 'name' );
	}

	public function mailContentType(): string {
		return 'text/html';
	}
}