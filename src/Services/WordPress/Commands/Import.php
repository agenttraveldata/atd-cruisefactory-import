<?php


namespace ATD\CruiseFactory\Services\WordPress\Commands;


use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use DateTime;

class Import {
	private array $friendlyFeedNames = [
		'cruises-with-ports'   => 'cruises',
		'deckplans'            => 'decks',
		'sailingdates'         => 'departures',
		'specialsailingdates'  => 'special-departures',
		'cruiselines'          => 'cruise-lines',
		'cabincategorypricing' => 'cruise-pricing'
	];

	public function __construct() {
		if ( class_exists( 'WP_CLI' ) ) {
			define( 'ATD_CF_XML_USING_CLI', true );
		}
	}

	/**
	 * Import Cruise Factory XML into WordPress.
	 *
	 * ## OPTIONS
	 *
	 * [<name>...]
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
	 *   - cruise-pricing
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
	 * ---
	 *
	 * [--overwrite-posts]
	 * : Whether to overwrite post details in WordPress
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
		if ( ! empty( $args_assoc['images'] ) && $args_assoc['images'] === 'overwrite' ) {
			define( 'ATD_CF_XML_IMAGE_OVERWRITE', true );
		}

		if ( ! empty( $args_assoc['cache'] ) && $args_assoc['cache'] === 'invalidate' ) {
			define( 'ATD_CF_XML_IMPORT_NO_CACHE', true );
		}

		if ( ! empty( $args_assoc['overwrite-posts'] ) ) {
			define( 'ATD_CF_XML_IMPORT_FORCE_OVERWRITE', true );
		}

		if ( empty( $args_assoc['wordpress'] ) ) {
			$args_assoc['wordpress'] = 'import';
		}

		if ( ! $feedObjects = $this->getFeedObjects( $args ) ) {
			return;
		}

		foreach ( $feedObjects as $feedName => $feedObject ) {
			if ( $args_assoc['wordpress'] === 'only' ) {
				$this->logGeneral( 'Importing ' . $feedName . ' from database into WordPress' );
				if ( $feedObject->import( new DateTime( '-50 years' ) ) ) {
					$this->logSuccess( 'Imported ' . $feedName . ' WordPress posts.' );
				}
				continue;
			}

			$this->logGeneral( 'Fetching Cruise Factory services XML for ' . $feedName );
			if ( $updatedAt = $feedObject->fetchServices() ) {
				$this->logSuccess( 'Imported XML from Cruise Factory into database.' );

				if ( $dateTime = DateTime::createFromFormat( ATD_CF_XML_DATE_FORMAT, $updatedAt ) ) {
					if ( $args_assoc['wordpress'] === 'import' ) {
						$this->logGeneral( 'Importing ' . $feedName . ' from database into WordPress' );
						if ( $feedObject->import( $dateTime ) ) {
							$this->logSuccess( 'Imported ' . $feedName . ' WordPress posts.' );
						}
					}
				}
			}
		}

		$this->logGeneral( 'Import complete.' );
	}

	/**
	 * Import Cruise Factory XML into WordPress.
	 *
	 * ## OPTIONS
	 *
	 * [<name>...]
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
	 *   - cruise-pricing
	 * ---
	 *
	 * [--wordpress=<wordpress>]
	 * : Whether to import XML data as WordPress posts
	 * ---
	 * default: import
	 * options:
	 *   - import
	 *   - exclude
	 *   - only
	 * ---
	 *
	 * [--images=<images>]
	 * : Whether to import images into WordPress
	 * ---
	 * default: overwrite
	 * options:
	 *   - overwrite
	 *   - exclude
	 * ---
	 *
	 * [--overwrite-posts]
	 * : Whether to overwrite post details in WordPress
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

		if ( ! empty( $args_assoc['images'] ) && $args_assoc['images'] === 'overwrite' ) {
			define( 'ATD_CF_XML_IMAGE_OVERWRITE', true );
		}

		if ( ! empty( $args_assoc['cache'] ) && $args_assoc['cache'] === 'invalidate' ) {
			define( 'ATD_CF_XML_IMPORT_NO_CACHE', true );
		}

		if ( ! empty( $args_assoc['overwrite-posts'] ) ) {
			define( 'ATD_CF_XML_IMPORT_FORCE_OVERWRITE', true );
		}

		if ( empty( $args_assoc['wordpress'] ) ) {
			$args_assoc['wordpress'] = 'import';
		}

		if ( ! $feedObjects = $this->getFeedObjects( $args ) ) {
			return;
		}

		foreach ( $feedObjects as $feedName => $feedObject ) {
			if ( $args_assoc['wordpress'] === 'only' ) {
				$this->logGeneral( 'Importing ' . $feedName . ' from database into WordPress' );
				if ( $feedObject->import( new DateTime( '-50 years' ) ) ) {
					$this->logSuccess( 'Imported ' . $feedName . ' WordPress posts.' );
				}
				continue;
			}

			$this->logGeneral( 'Fetching Cruise Factory incremental XML for ' . $feedName );
			if ( $updatedAt = $feedObject->fetchIncrement() ) {
				$this->logSuccess( 'Imported XML from Cruise Factory into database.' );

				if ( $dateTime = DateTime::createFromFormat( ATD_CF_XML_DATE_FORMAT, $updatedAt ) ) {
					if ( $args_assoc['wordpress'] === 'import' ) {
						$this->logGeneral( 'Importing ' . $feedName . ' from database into WordPress' );
						if ( $feedObject->import( $dateTime ) ) {
							$this->logSuccess( 'Imported ' . $feedName . ' WordPress posts.' );
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

		$this->logGeneral( 'Import complete.' );
	}

	/**
	 * @param string $paramFeed
	 *
	 * @return Feed\Feed[]|null
	 */
	private function getFeedObjects( array $paramFeeds ): ?array {
		$realFeedNames = array_flip( $this->friendlyFeedNames );

		/** @var array<string, Feed\Feed> $feedObjects */
		$feedObjects = [];

		if ( in_array( 'all', $paramFeeds ) ) {
			$paramFeeds = [ 'all' ];
		}

		foreach ( $paramFeeds as $paramFeed ) {
			foreach ( Feed\Provider::getPublicFeeds( true ) as $feed ) {
				/** @var Feed\Feed $feedName */
				$feedName = $feed['name'];

				if ( $paramFeed === 'all' || ( $realFeedNames[ $paramFeed ] ?? $paramFeed ) === $feedName::getFeedName() ) {
					$feedObjects[ $this->friendlyFeedNames[ $feedName::getFeedName() ] ?? $feedName::getFeedName() ] = new $feedName();
				}
			}
		}

		if ( empty( $feedObjects ) ) {
			try {
				$this->logError( 'Could not find any feed to import!' );
			} catch ( \WP_CLI\ExitException $e ) {
			}

			return null;
		}

		return $feedObjects;
	}

	private function logGeneral( string $message ) {
		if ( defined( 'ATD_CF_XML_USING_CLI' ) ) {
			\WP_CLI::log( $message );
		}
	}

	private function logSuccess( string $message ) {
		if ( defined( 'ATD_CF_XML_USING_CLI' ) ) {
			\WP_CLI::success( $message );
		}
	}

	/**
	 * @param string $message
	 *
	 * @throws \WP_CLI\ExitException
	 */
	private function logError( string $message ) {
		if ( defined( 'ATD_CF_XML_USING_CLI' ) ) {
			\WP_CLI::error( $message );
		}
	}
}