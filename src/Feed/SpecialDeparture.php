<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Post;
use DateTimeInterface;

class SpecialDeparture extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_special_departure';
	protected static string $feedName = 'specialsailingdates';
	public static string $metaKeyId = 'atd_cfi_special_departure_id';
	protected static string $entity = Entity\SpecialDeparture::class;
	public array $dependencies = [
		Departure::class,
		Special::class
	];
	protected array $relationships = [
		'sailingdate_id' => Entity\Departure::class,
		'special_id'     => Entity\Special::class
	];

	public function import( DateTimeInterface $updatedAt ): bool {
		foreach ( $this->fetchRowsToImport( [ 'i.updated_at >=' => $updatedAt->format( ATD_CF_XML_DATE_FORMAT ) ] ) as $row ) {
			Post\Departure::add( $row );
		}

		return true;
	}

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factory_id` int NOT NULL DEFAULT '0',
  `special_id` int DEFAULT NULL,
  `sailingdate_id` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sailingdate` (`sailingdate_id`),
  KEY `special` (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}