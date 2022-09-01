<?php


namespace ATD\CruiseFactory\Controller\Api\v1;


use ATD\CruiseFactory\Controller\AbstractController;
use ATD\CruiseFactory\Feed\CruiseLine;

class CruiseLineController extends AbstractController {
	protected string $apiEndpointPrefix = '/cruise-line';

	public function __construct() {
		$this->setFeed( new CruiseLine() );
	}
}