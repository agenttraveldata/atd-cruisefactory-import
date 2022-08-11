<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class CruiseType extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_cruise_type';
	protected static string $feedName = 'cruisetypes';
	protected static string $entity = Entity\CruiseType::class;

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
  `name` varchar(100) NOT NULL DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}