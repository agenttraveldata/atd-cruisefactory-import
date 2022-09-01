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
	}
}