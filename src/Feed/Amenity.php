<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class Amenity extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_ship_amenity';
	protected static string $feedName = 'amenities';
	protected static string $entity = Entity\Amenity::class;

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
  `ship_id` int NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipid` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}