<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class Port extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_port';
	protected static string $feedName = 'ports';
	protected static string $entity = Entity\Port::class;
	public array $dependencies = [ LatLong::class ];
	protected array $relationships = [ Entity\LatLong::class => 'port_id' ];

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_id` int NOT NULL DEFAULT '0',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `photo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `destination` (`destination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}