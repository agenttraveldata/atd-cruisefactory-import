<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Services\Form\Validators\EnquiryValidator;
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
			$fields = $validator->getSanitizedFields();
			wp_send_json_success( [ 'message' => 'Thank you for your enquiry!' ] );
		}

		wp_send_json_error( [ 'message' => 'Your message has the following issues:<ul>' . $validator->getMessages() . '</ul>' ] );
	}
}