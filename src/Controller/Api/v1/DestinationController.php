<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Feed\Destination;

class DestinationController extends AbstractController {
	protected string $apiEndpointPrefix = '/destination';

	public function __construct() {
		$this->setFeed( new Destination() );
	}
}