<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class SpecialPrice extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_special_price';
	protected static string $feedName = 'specialspricing';
	protected static string $entity = Entity\SpecialPrice::class;
	protected array $relationships = [ 'cabin_id' => Entity\Cabin::class ];

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
  `factory_id` int NOT NULL DEFAULT '0',
  `special_id` int NOT NULL DEFAULT '0',
  `cruise_id` int NOT NULL DEFAULT '0',
  `cabin_id` int NOT NULL DEFAULT '0',
  `price` double(10,2) DEFAULT NULL,
  `portcharges` char(1) NOT NULL DEFAULT 'e',
  `currency_id` int NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `specialid` (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}