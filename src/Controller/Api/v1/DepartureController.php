<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Controller\Helper\ItineraryTrait;
use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use WP_REST_Request;
use WP_REST_Response;

class DepartureController extends AbstractController {
	use ItineraryTrait;

	protected string $apiEndpointPrefix = '/departure';

	public function __construct() {
		$this->setFeed( new Feed\Departure() );

		$this->addRoute( '/(?P<id>\d+)/itinerary', [ $this, 'itinerary' ] )
		     ->addRoute( '/(?P<id>\d+)/summary', [ $this, 'summary' ] );
	}

	public function itinerary( WP_REST_Request $request ): WP_REST_Response {
		$itinerary = [];
		if ( $request->has_param( 'id' ) ) {
			/** @var Entity\Departure $departure */
			if ( $departure = $this->getFeed()->getEntityManager()->getMapper( $this->getFeed()->getEntity() )->find( $request->get_param( 'id' ) ) ) {
				$itinerary = $this->formatItinerary( $departure->getSailingDate(), $departure );
			}
		}

		return new WP_REST_Response( [ [ 'html_response' => $itinerary ] ] );
	}

	public function summary( WP_REST_Request $request ): void {
		$itinerary = [];
		if ( $request->has_param( 'id' ) ) {
			$itinerary = $request->get_param( 'id' );
		}

		wp_send_json_success( [ 'html_response' => $itinerary ] );
	}
}