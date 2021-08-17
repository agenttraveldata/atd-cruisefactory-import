<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Feed\Destination;
use DateTime;
use WP_REST_Request;
use WP_REST_Server;

class DestinationController extends AbstractController {
	protected string $apiEndpointPrefix = '/destination';

	public function __construct() {
		$this->setFeed( new Destination() );

		$this->addRoute( '/fetch/services', [ $this, 'services' ] )
		     ->addRoute( '/fetch/increment', [ $this, 'increment' ] )
		     ->addRoute( '/import', [ $this, 'import' ], true, WP_REST_Server::CREATABLE );
	}

	public function import( WP_REST_Request $request ): array {
		$data = $request->get_json_params();

		if ( ! empty( $data['updatedAt'] ) ) {
			if ( $updatedAt = DateTime::createFromFormat( ATD_CF_XML_DATE_FORMAT, $data['updatedAt'] ) ) {
				$this->getFeed()->import( $updatedAt );

				return [ 'import' => true ];
			}
		}


		return [ 'import' => false ];
	}

	public function services(): array {
		if ( $updatedAt = $this->getFeed()->fetchServices() ) {
			return [ 'services' => $updatedAt ];
		}

		return [ 'services' => false ];
	}

	public function increment(): array {
		return [ 'increment' ];
	}
}