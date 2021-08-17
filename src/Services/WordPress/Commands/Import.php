<?php


namespace ATD\CruiseFactory\Services\WordPress\Commands;


use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use DateTime;
use WP_CLI;

class Import {
	private array $friendlyFeedNames = [
		'cruises-with-ports'  => 'cruises',
		'deckplans'           => 'decks',
		'sailingdates'        => 'departures',
		'specialsailingdates' => 'special-departures',
		'cruiselines'         => 'cruise-lines'
	];

	/**
	 * Import Cruise Factory XML into WordPress.
	 *
	 * ## OPTIONS
	 *
	 * [<name>]
	 * : The name of the feed to import.
	 * ---
	 * default: all
	 * options:
	 *   - all
	 *   - destinations
	 *   - cruise-lines
	 *   - ships
	 *   - cabins
	 *   - decks
	 *   - ports
	 *   - departures
	 *   - special-departures
	 *
	 * [--wordpress=<wordpress>]
	 * : Whether to import XML data as WordPress posts
	 * ---
	 * default: import
	 * options:
	 *   - import
	 *   - exclude
	 *   - only
	 *
	 * [--images=<images>]
	 * : Whether to import images into WordPress
	 * ---
	 * default: overwrite
	 * options:
	 *   - overwrite
	 *   - exclude
	 *
	 * [--cache=<cache>]
	 * : Whether to use cached XML files or invalid and re-download XML file from Cruise Factory
	 * ---
	 * default: cache
	 * options:
	 *    - cache
	 *    - invalidate
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp atd import services departures --wordpress=exclude
	 *
	 */
	public function services( array $args, array $args_assoc ): void {
		if ( $args_assoc['images'] === 'overwrite' ) {
			define( 'ATD_CF_XML_IMAGE_OVERWRITE', true );
		}

		if ( $args_assoc['cache'] === 'invalidate' ) {
			define( 'ATD_CF_XML_IMPORT_NO_CACHE', true );
		}

		if ( ! $feedObjects = $this->getFeedObjects( $args[0] ) ) {
			return;
		}

		foreach ( $feedObjects as $feedName => $feedObject ) {
			if ( $args_assoc['wordpress'] === 'only' ) {
				WP_CLI::log( 'Importing ' . $feedName . ' from database into WordPress' );
				if ( $feedObject->import( new DateTime( '-50 years' ) ) ) {
					WP_CLI::success( 'Imported ' . $feedName . ' WordPress posts.' );
				}
				continue;
			}

			WP_CLI::log( 'Fetching Cruise Factory services XML for ' . $feedName );
			if ( $updatedAt = $feedObject->fetchServices() ) {
				WP_CLI::success( 'Imported XML from Cruise Factory into database.' );

				if ( $dateTime = DateTime::createFromFormat( ATD_CF_XML_DATE_FORMAT, $updatedAt ) ) {
					if ( $args_assoc['wordpress'] === 'import' ) {
						WP_CLI::log( 'Importing ' . $feedName . ' from database into WordPress' );
						if ( $feedObject->import( $dateTime ) ) {
							WP_CLI::success( 'Imported ' . $feedName . ' WordPress posts.' );
						}
					}
				}
			}
		}

		WP_CLI::log( 'Import complete.' );
	}

	/**
	 * Import Cruise Factory XML into WordPress.
	 *
	 * ## OPTIONS
	 *
	 * [<name>]
	 * : The name of the feed to import.
	 * ---
	 * default: all
	 * options:
	 *   - all
	 *   - destinations
	 *   - cruise-lines
	 *   - ships
	 *   - cabins
	 *   - decks
	 *   - ports
	 *   - departures
	 *   - special-departures
	 *
	 * [--wordpress=<wordpress>]
	 * : Whether to import XML data as WordPress posts
	 * ---
	 * default: import
	 * options:
	 *   - import
	 *   - exclude
	 *   - only
	 *
	 * [--images=<images>]
	 * : Whether to import images into WordPress
	 * ---
	 * default: overwrite
	 * options:
	 *   - overwrite
	 *   - exclude
	 *
	 * [--cache=<cache>]
	 * : Whether to use cached XML files or invalid and re-download XML file from Cruise Factory
	 * ---
	 * default: cache
	 * options:
	 *    - cache
	 *    - invalidate
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp atd import increment departures --wordpress=exclude
	 *
	 */
	public function increment( array $args, array $args_assoc ): void {
		define( 'ATD_CF_XML_LOG_UPDATES', true );
		Logger::info( 'Incremental import started.' );

		if ( $args_assoc['images'] === 'overwrite' ) {
			define( 'ATD_CF_XML_IMAGE_OVERWRITE', true );
		}

		if ( $args_assoc['cache'] === 'invalidate' ) {
			define( 'ATD_CF_XML_IMPORT_NO_CACHE', true );
		}

		if ( ! $feedObjects = $this->getFeedObjects( $args[0] ) ) {
			return;
		}

		foreach ( $feedObjects as $feedName => $feedObject ) {
			if ( $args_assoc['wordpress'] === 'only' ) {
				WP_CLI::log( 'Importing ' . $feedName . ' from database into WordPress' );
				if ( $feedObject->import( new DateTime( '-50 years' ) ) ) {
					WP_CLI::success( 'Imported ' . $feedName . ' WordPress posts.' );
				}
				continue;
			}

			WP_CLI::log( 'Fetching Cruise Factory incremental XML for ' . $feedName );
			if ( $updatedAt = $feedObject->fetchIncrement() ) {
				WP_CLI::success( 'Imported XML from Cruise Factory into database.' );

				if ( $dateTime = DateTime::createFromFormat( ATD_CF_XML_DATE_FORMAT, $updatedAt ) ) {
					if ( $args_assoc['wordpress'] === 'import' ) {
						WP_CLI::log( 'Importing ' . $feedName . ' from database into WordPress' );
						if ( $feedObject->import( $dateTime ) ) {
							WP_CLI::success( 'Imported ' . $feedName . ' WordPress posts.' );
						}
					}
				}
			}
		}

		Logger::info( 'Incremental import completed.' );

		if ( $args_assoc['wordpress'] !== 'exclude' ) {
			Logger::emailLogs();
		}

		Logger::destroy();

		WP_CLI::log( 'Import complete.' );
	}

	/**
	 * @param string $paramFeed
	 *
	 * @return Feed\Feed[]|null
	 * @throws WP_CLI\ExitException
	 */
	private function getFeedObjects( string $paramFeed ): ?array {
		$realFeedNames = array_flip( $this->friendlyFeedNames );

		/** @var array<string, Feed\Feed> $feedObjects */
		$feedObjects = [];

		foreach ( Feed\Provider::getPublicFeeds( true ) as $feed ) {
			/** @var Feed\Feed $feedName */
			$feedName = $feed['name'];

			if ( $paramFeed === 'all' || ( $realFeedNames[ $paramFeed ] ?? $paramFeed ) === $feedName::getFeedName() ) {
				$feedObjects[ $this->friendlyFeedNames[ $feedName::getFeedName() ] ?? $feedName::getFeedName() ] = new $feedName();
			}
		}

		if ( empty( $feedObjects ) ) {
			WP_CLI::error( 'Could not find ' . $paramFeed . ' feed to import!' );

			return null;
		}

		return $feedObjects;
	}
}