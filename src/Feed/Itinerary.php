<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class Itinerary extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_itinerary';
	protected static string $feedName = 'itineraries';
	protected static string $entity = Entity\Itinerary::class;
	protected array $relationships = [
		'port_id' => Entity\Port::class
	];

	public function import( DateTimeInterface $updatedAt ): bool {
		return false;
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
  `cruise_id` int NOT NULL DEFAULT '0',
  `day` int DEFAULT NULL,
  `port_id` int NOT NULL DEFAULT '0',
  `arrive` varchar(50) DEFAULT NULL,
  `depart` varchar(50) DEFAULT NULL,
  `portorder` smallint NOT NULL DEFAULT '100',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cruiseId` (`cruise_id`),
  KEY `portId` (`port_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}