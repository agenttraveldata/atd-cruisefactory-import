<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Feed\Cruise;
use WP_REST_Request;
use WP_REST_Server;

class CruiseController extends AbstractController {
	protected string $apiEndpointPrefix = '/cruise';

	public function __construct() {
		$this->setFeed( new Cruise() );

		$this->addRoute( '/fetch/services', [ $this, 'services' ] )
		     ->addRoute( '/fetch/increment', [ $this, 'increment' ] )
		     ->addRoute( '/import', [ $this, 'import' ], true, WP_REST_Server::CREATABLE );
	}

	public function import( WP_REST_Request $request ): array {
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