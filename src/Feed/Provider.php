<?php


namespace ATD\CruiseFactory\Feed;


class Provider {
	private static array $feeds = [
		CleanUp::class,
		Log::class,
		Currency::class,
		Factory::class,
		Destination::class,
		LatLong::class,
		Port::class,
		CruiseLine::class,
		Ship::class,
		Cabin::class,
		Deck::class,
		Amenity::class,
		Facility::class,
		Cruise::class,
		Special::class,
		Itinerary::class,
		Departure::class,
		CruisePrice::class,
		SpecialPrice::class,
		SpecialItinerary::class,
		SpecialDeparture::class,
		SpecialLeadPrice::class
	];

	public static function registerTables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/** @var Feed $feed */
		foreach ( self::$feeds as $feed ) {
			dbDelta( $feed::getCreateTableSchema() );
		}

		update_option( ATD_CF_XML_DB_VERSION_FIELD, ATD_CF_DATABASE_VERSION );
	}

	public static function getFeeds(): array {
		return self::$feeds;
	}

	public static function getPublicFeeds( bool $fullClassName = false ): array {
		$invalid = [
			CleanUp::class,
			Log::class,
			Amenity::class,
			Facility::class,
			Factory::class,
			Currency::class,
			Itinerary::class,
			SpecialPrice::class,
			SpecialLeadPrice::class,
			SpecialItinerary::class
		];

		$feeds = array_filter( self::$feeds, function ( $f ) use ( $invalid ) {
			return ! in_array( $f, $invalid );
		} );

		return array_filter( array_map( function ( $feed ) use ( $fullClassName ) {
			if ( class_exists( $feed ) ) {
				$feedName = $feed;
				if ( ! $fullClassName ) {
					$feedName = substr( strrchr( $feedName, '\\' ), 1 );
				}

				/** @var Feed $feed */
				$feed = new $feed();

				return [ 'name' => $feedName, 'last_updated' => $feed->getLastUpdate() ];
			}

			return null;
		}, $feeds ) );
	}
}