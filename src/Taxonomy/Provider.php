<?php


namespace ATD\CruiseFactory\Taxonomy;


class Provider {
	public static array $taxonomies = [
		CruiseLine::class,
		CruiseType::class,
		PromoCode::class,
		Destination::class,
		DisembarkPort::class,
		EmbarkPort::class,
		Duration::class,
		Ship::class,
		DepartureType::class,
		SpecialType::class,
		Month::class
	];

	public static function registerTaxonomies() {
		/** @var AbstractTaxonomy $taxonomy */
		foreach ( self::$taxonomies as $taxonomy ) {
			$taxonomy::register();
		}
	}
}