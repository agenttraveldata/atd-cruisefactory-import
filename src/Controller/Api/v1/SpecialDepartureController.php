<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Controller\Helper\ItineraryTrait;
use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed\SpecialDeparture;
use DateTime;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class SpecialDepartureController extends AbstractController {
	use ItineraryTrait;

	protected string $apiEndpointPrefix = '/special-departure';

	public function __construct() {
		$this->setFeed( new SpecialDeparture() );

		$this->addRoute( '/fetch/services', [ $this, 'services' ] )
		     ->addRoute( '/fetch/increment', [ $this, 'increment' ] )
		     ->addRoute( '/import', [ $this, 'import' ], true, WP_REST_Server::CREATABLE )
		     ->addRoute( '/(?P<id>\d+)/itinerary', [ $this, 'itinerary' ] );
	}

	public function itinerary( WP_REST_Request $request ): WP_REST_Response {
		$itinerary = [];
		if ( $request->has_param( 'id' ) ) {
			/** @var Entity\SpecialDeparture $specialDeparture */
			if ( $specialDeparture = $this->getFeed()->getEntityManager()->getMapper( Entity\SpecialDeparture::class )->find( $request->get_param( 'id' ) ) ) {
				$itinerary = $this->formatItinerary( $specialDeparture->getSailingdate()->getSailingDate(), $specialDeparture );
			}
		}

		return new WP_REST_Response( [ [ 'html_response' => $itinerary ] ] );
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