<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Feed\Ship;

class ShipController extends AbstractController {
	protected string $apiEndpointPrefix = '/ship';

	public function __construct() {
		$this->setFeed( new Ship() );
	}
}