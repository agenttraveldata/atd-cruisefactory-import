<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;

class Ship extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_ship';
	protected static string $feedName = 'ships';
	protected static string $entity = Entity\Ship::class;
	public array $dependencies = [ Cabin::class, Amenity::class, Facility::class ];
	protected static array $collections = [ 'ship_id' => [ Entity\Amenity::class, Entity\Facility::class ] ];
	protected static array $relationships = [ 'cruiseline_id' => Entity\CruiseLine::class ];

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL,
  `cruiseline_id` int NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL DEFAULT '',
  `thumbnail` varchar(50) DEFAULT NULL,
  `mainimage` varchar(50) DEFAULT NULL,
  `maidenvoyage` varchar(100) DEFAULT NULL,
  `refurbished` varchar(100) DEFAULT NULL,
  `tonnage` varchar(50) DEFAULT NULL,
  `length` varchar(50) DEFAULT NULL,
  `beam` varchar(50) DEFAULT NULL,
  `draft` varchar(50) DEFAULT NULL,
  `speed` varchar(50) DEFAULT NULL,
  `ship_rego` varchar(150) DEFAULT NULL,
  `pass_capacity` varchar(50) DEFAULT NULL,
  `pass_space` varchar(50) DEFAULT NULL,
  `crew_size` varchar(50) DEFAULT NULL,
  `nat_crew` varchar(100) DEFAULT NULL,
  `nat_officers` varchar(100) DEFAULT NULL,
  `nat_dining` varchar(100) DEFAULT NULL,
  `description` text,
  `star_rating` int DEFAULT NULL,
  `cruisetype_id` tinyint unsigned NOT NULL DEFAULT '0',
  `currency_id` int unsigned DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cruise_line` (`cruiseline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}