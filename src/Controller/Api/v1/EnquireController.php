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
		$this->addRoute( '/send', [ $this, 'send' ], false, WP_REST_Server::CREATABLE );
	}

	public function send( WP_REST_Request $request ): void {
		$validator = new EnquiryValidator();

		if ( $validator->validate() ) {
			if ( $secretKey = get_option( ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD ) ) {
				$reCaptcha = new ReCaptcha( $secretKey );
				if ( ! $reCaptcha->validate( $_POST['g-recaptcha-response'] ) ) {
					wp_send_json_error( [ 'message' => 'Google reCaptcha failed.' ] );
				}
			}

			$fields = $validator->getSanitizedFields();
			// send form

			wp_send_json_success( [ 'message' => 'Thank you for your enquiry!' ] );
		}

		wp_send_json_error( [ 'message' => 'Your message has the following issues:<ul>' . $validator->getMessages() . '</ul>' ] );
	}
}