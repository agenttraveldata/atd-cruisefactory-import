<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;

class Log {
	protected static string $tableName = 'atd_cfi_log';
	protected static string $entity = Entity\Log::class;

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getTableName( string $prefix = '' ): string {
		return $prefix . static::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(48) NOT NULL DEFAULT '',
  `message` text NOT NULL DEFAULT '',
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}