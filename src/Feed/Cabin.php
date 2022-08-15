<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;

class Cabin extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_cabin';
	protected static string $feedName = 'cabins-with-category';
	public static string $metaKeyId = 'atd_cfi_cabin_id';
	protected static string $entity = Entity\Cabin::class;
	protected static array $relationships = [ 'ship_id' => Entity\Ship::class ];

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ship_id` int NOT NULL DEFAULT '0',
  `cabin_code` varchar(28) DEFAULT NULL,
  `cabin_category` varchar(12) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text,
  `image` varchar(150) DEFAULT NULL,
  `photo` varchar(150) DEFAULT NULL,
  `cabin_order` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipid` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}