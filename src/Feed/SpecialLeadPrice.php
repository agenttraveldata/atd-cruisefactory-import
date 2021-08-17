<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class SpecialLeadPrice extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_special_lead_price';
	protected static string $feedName = 'leadpricing';
	protected static string $entity = Entity\SpecialLeadPrice::class;

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
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `special_id` int(11) unsigned NOT NULL DEFAULT '0',
  `price_inside` double(9,2) DEFAULT '0.00',
  `price_outside` double(9,2) DEFAULT '0.00',
  `price_balcony` double(9,2) DEFAULT '0.00',
  `price_suites` double(9,2) DEFAULT '0.00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `specialId` (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}