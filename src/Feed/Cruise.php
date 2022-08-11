<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class Cruise extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_cruise';
	protected static string $feedName = 'cruises-with-ports';
	protected static string $entity = Entity\Cruise::class;
	public array $dependencies = [
		Destination::class,
		Port::class,
		Itinerary::class,
		CruiseLine::class,
		Ship::class,
		CruiseType::class
	];
	protected static array $collections = [ 'cruise_id' => [ Entity\Itinerary::class ] ];
	protected static array $relationships = [
		'destination_id'   => Entity\Destination::class,
		'cruiseline_id'    => Entity\CruiseLine::class,
		'ship_id'          => Entity\Ship::class,
		'embarkport_id'    => Entity\Port::class,
		'disembarkport_id' => Entity\Port::class,
		'cruisetype_id'    => Entity\CruiseType::class
	];

	public function import( DateTimeInterface $updatedAt ): bool {
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
  `cruiseline_id` int NOT NULL DEFAULT '0',
  `destination_id` int NOT NULL DEFAULT '0',
  `ship_id` int NOT NULL DEFAULT '0',
  `cruisetype_id` smallint NOT NULL DEFAULT '0',
  `length` smallint DEFAULT NULL,
  `name` varchar(140) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `brief_description` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `photo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `start_price` decimal(9,2) DEFAULT NULL,
  `currency_id` int NOT NULL DEFAULT '1',
  `cruise_order` int NOT NULL DEFAULT '100',
  `embarkport_id` int NOT NULL DEFAULT '0',
  `disembarkport_id` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cruiseline_id` (`cruiseline_id`),
  KEY `destination_id` (`destination_id`),
  KEY `ship_id` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}