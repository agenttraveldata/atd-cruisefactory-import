<?php


namespace ATD\CruiseFactory\Controller;


class Provider {
	public static function registerControllers() {
		new Api\v1\DepartureController();
		new Api\v1\SpecialDepartureController();
		new Api\v1\DestinationController();
		new Api\v1\CruiseLineController();
		new Api\v1\ShipController();
		new Api\v1\CruiseController();
		new Api\v1\SearchController();
	}
}