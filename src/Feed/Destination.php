<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity\Destination as Entity;

class Destination extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_destination';
	protected static string $feedName = 'destinations';
	protected static string $entity = Entity::class;

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `banner` varchar(100) DEFAULT NULL,
  `map_thumb` varchar(100) DEFAULT NULL,
  `map_large` varchar(100) DEFAULT NULL,
  `featured` char(1) NOT NULL DEFAULT 'n',
  `featured_text` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}