<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Post;
use ATD\CruiseFactory\Services\Data\DBAL\EntityManager;
use DateTime;
use DateTimeInterface;
use Exception;
use wpdb;
use XMLReader;

abstract class AbstractFeed implements Feed {
	public array $dependencies = [];
	private static array $fetchedFeeds = [];
	protected static string $tableName = '';
	protected static string $feedName = '';
	public static string $metaKeyId = 'atd_cfi_id';
	protected static ?string $forceServiceType = null;
	protected static string $entity = '';
	protected array $collections = [];
	protected array $relationships = [];
	protected array $xmlFieldModifiers = [];
	protected int $expiryTime = 600;
	protected string $updatedAt;
	protected wpdb $wpdb;
	private CleanUp $cleanUp;
	private EntityManager $entityManager;

	public function __construct() {
		global $wpdb;

		$this->cleanUp = new CleanUp();
		$this->wpdb    = $wpdb;

		$this->entityManager = new EntityManager( $wpdb->__get( 'dbh' ) );
	}

	public function getEntityManager(): EntityManager {
		return $this->entityManager;
	}

	public function getRelationships(): array {
		return $this->relationships;
	}

	public function getCollections(): array {
		return $this->collections;
	}

	public function getEntity(): string {
		return static::$entity;
	}

	public static function getTableName( string $prefix = '' ): string {
		return $prefix . static::$tableName;
	}

	public static function getFeedName(): string {
		return static::$feedName;
	}

	protected function postServicesPopulateCleanUp(): void {
		if ( $this->updatedAt ) {
			$tableName = $this->wpdb->prefix . static::$tableName;
			if ( $rows = $this->wpdb->get_results( 'select id from ' . $tableName . ' where updated_at < "' . $this->updatedAt . '"' ) ) {
				$this->cleanUp->add( $tableName, array_column( $rows, 'id' ), static::$metaKeyId );
			}
		}
	}

	protected function performCleanUp(): void {
		if ( $rows = $this->wpdb->get_results( 'SELECT * FROM ' . CleanUp::getTableNameWithPrefix() ) ) {
			foreach ( $rows as $row ) {
				if ( $row->table_name === SpecialDeparture::getTableNameWithPrefix() ) {
					/* this particular removal requires a few extra steps */
					if ( $this->isLastSpecialForCruise( (int) $row->row_id ) ) {
						$this->convertSpecialToCruise( (int) $row->row_id );
					}
				}

				if ( false !== $this->wpdb->delete( $row->table_name, [ 'id' => (int) $row->row_id ] ) ) {
					$this->cleanUp->removeWordPressPostAndRelated( $row->table_name, (int) $row->row_id, $row->meta_key );
					$this->wpdb->delete( CleanUp::getTableNameWithPrefix(), [ 'id' => (int) $row->id ] );
				}
			}
		}
	}

	private function convertSpecialToCruise( int $specialDepartureId ): void {
		/** @var Entity\SpecialDeparture $specialDeparture */
		if ( $specialDeparture = $this->entityManager->getMapper( Entity\SpecialDeparture::class )->find( $specialDepartureId ) ) {
			if ( Post\Departure::convertBackToCruise( $specialDeparture ) ) {
				Post\Departure::add( $specialDeparture->getSailingdate() );
			}
		}
	}

	private function isLastSpecialForCruise( int $specialDepartureId ): bool {
		if ( $rows = $this->wpdb->get_results(
			'SELECT id FROM ' . SpecialDeparture::getTableNameWithPrefix() .
			' WHERE sailingdate_id = (SELECT sailingdate_id FROM ' . SpecialDeparture::getTableNameWithPrefix() .
			' WHERE id = ' . $specialDepartureId . ')', ARRAY_N
		) ) {
			if ( count( $rows ) === 1 ) {
				return true;
			}
		}

		return false;
	}

	protected function fetchRowsToImport( array $where = [] ): iterable {
		if ( ! defined( 'WP_IMPORTING' ) ) {
			define( 'WP_IMPORTING', true );
		}

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		$chunkCounter = 0;
		while ( true ) {
			if ( ! $rows = $this->getEntityManager()->getMapper( $this->getEntity() )->findBy( $where, $chunkCounter ) ) {
				break;
			}

			foreach ( $rows as $row ) {
				yield $row;
			}

			$chunkCounter += ATD_CF_XML_CHUNK_LIMIT;
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
	}

	protected function fetchRowFromXmlFile( string $xmlFileName, string $elementToFetch = 'row' ): iterable {
		$xmlReader = new XMLReader();
		$xmlReader->open( $xmlFileName );

		$row         = [];
		$elementName = '';

		while ( $xmlReader->read() ) {
			switch ( $xmlReader->nodeType ) {
				case XMLReader::ELEMENT:
					$elementName = strtolower( $xmlReader->name );
					if ( $elementName === $elementToFetch ) {
						$row = [];
					}
					break;
				case XMLReader::END_ELEMENT:
					if ( strtolower( $xmlReader->name ) === $elementToFetch ) {
						yield $row;
					}
					break;
				case XMLReader::TEXT:
				case XMLReader::CDATA:
					if ( $elementName !== $elementToFetch ) {
						$row[ $elementName ] = $xmlReader->value;
					}
					break;
			}
		}
	}

	public function fetchServices(): ?string {
		foreach ( $this->dependencies as $dependency ) {
			if ( ! isset( self::$fetchedFeeds[ $dependency ] ) ) {
				self::$fetchedFeeds[ $dependency ] = true;
				( new $dependency() )->fetchServices();
			}
		}

		if ( $xmlFileName = $this->fetchFromCruiseFactory( 'services', static::$feedName ) ) {
			$this->updatedAt = wp_date( ATD_CF_XML_DATE_FORMAT );
			$this->importXmlToDatabase( $xmlFileName );
			$this->postServicesPopulateCleanUp();
			$this->performCleanUp();

			/* Add class to fetched list to prevent is being imported twice */
			self::$fetchedFeeds[ static::class ] = true;

			return $this->updatedAt;
		}

		return null;
	}

	public function import( DateTimeInterface $updatedAt ): bool {
		/** @var Post\Post $post */
		$post = str_replace( '\Feed', '\Post', static::class );

		foreach ( $this->fetchRowsToImport( [ 'i.updated_at >=' => $updatedAt->format( ATD_CF_XML_DATE_FORMAT ) ] ) as $row ) {
			$post::add( $row );
		}

		return true;
	}

	public function fetchIncrement(): ?string {
		foreach ( $this->dependencies as $dependency ) {
			if ( ! isset( self::$fetchedFeeds[ $dependency ] ) ) {
				self::$fetchedFeeds[ $dependency ] = true;
				( new $dependency() )->fetchIncrement();
			}
		}

		$serviceType = 'increment';
		if ( static::$forceServiceType === 'services' ) {
			$serviceType = 'services';
		}

		if ( $xmlFileName = $this->fetchFromCruiseFactory( $serviceType, static::$feedName ) ) {
			$this->updatedAt = wp_date( ATD_CF_XML_DATE_FORMAT );
			$this->importXmlToDatabase( $xmlFileName );

			if ( $serviceType === 'services' ) {
				$this->postServicesPopulateCleanUp();
			} else {
				foreach ( $this->fetchRowFromXmlFile( $xmlFileName, 'delete' ) as $delete ) {
					if ( isset( $delete['ids'] ) && ! empty( $delete['ids'] ) ) {
						$this->cleanUp->add( $this->wpdb->prefix . static::$tableName, array_map( 'intval', explode( ',', $delete['ids'] ) ), static::$metaKeyId );
					}
				}
			}

			$this->performCleanUp();

			/* Add class to fetched list to prevent is being imported twice */
			self::$fetchedFeeds[ static::class ] = true;

			return $this->updatedAt;
		}

		return null;
	}

	private function importXmlToDatabase( string $xmlFileName ): void {
		foreach ( $this->fetchRowFromXmlFile( $xmlFileName ) as $row ) {
			if ( sizeof( $this->xmlFieldModifiers ) > 0 ) {
				foreach ( $this->xmlFieldModifiers as $field => $modifier ) {
					if ( isset( $row[ $field ] ) ) {
						$row[ $field ] = call_user_func( $modifier, $row[ $field ] );
					}
				}
			}

			$this->wpdb->replace( $this->wpdb->prefix . static::$tableName, $row );
		}
	}

	protected function fetchFromCruiseFactory( string $serviceType, string $feedName ): ?string {
		$feed = $this->getXmlFileFromServer( $feedName, $serviceType );

		if ( $feed && file_exists( $feed ) ) {
			$contentSnippet = strtolower( file_get_contents( $feed, false, null, 0, 20 ) );
			if ( false === strpos( $contentSnippet, 'invalid' ) && false === strpos( $contentSnippet, 'error' ) ) {
				return $feed;
			}
		}

		return null;
	}

	private function getXmlFileFromServer( string $feedName, string $serviceType = 'services' ): ?string {
		$outputFile = dirname( ATD_CF_PLUGIN_FILE ) . '/var/cache/' . $serviceType . '/' . $feedName . '.xml';

		if ( file_exists( $outputFile ) && ! defined( 'ATD_CF_XML_IMPORT_NO_CACHE' ) && filemtime( $outputFile ) > ( time() - $this->expiryTime ) ) {
			return $outputFile;
		}

		try {
			$feedFile = sprintf( 'http://feeds.cruisefactory.net/%s/%s/get/%s',
				$serviceType,
				get_site_option( ATD_CF_XML_KEY_FIELD ),
				$feedName
			);

			fclose( fopen( $outputFile, 'w' ) );

			$handle = fopen( $feedFile, 'r' );
			while ( ! feof( $handle ) ) {
				file_put_contents( $outputFile, fread( $handle, 2048 ), FILE_APPEND );
			}
			fclose( $handle );

			return $outputFile;
		} catch ( Exception $e ) {
			trigger_error( 'atd_cruisefactory_xml::' . $e->getMessage(), E_USER_NOTICE );
		}

		return null;
	}

	public function getLastUpdate(): ?DateTimeInterface {
		if ( $rows = $this->wpdb->get_results( 'SELECT updated_at FROM ' . static::getTableNameWithPrefix() . ' ORDER BY updated_at DESC LIMIT 1' ) ) {
			if ( $dateTime = DateTime::createFromFormat( 'Y-m-d H:i:s', $rows[0]->updated_at ) ) {
				return $dateTime;
			}
		}

		return null;
	}
}