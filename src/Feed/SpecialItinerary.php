<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class SpecialItinerary extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_special_itinerary';
	protected static string $feedName = 'specialitineraries';
	protected static string $entity = Entity\SpecialItinerary::class;

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
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `special_id` int unsigned NOT NULL DEFAULT '1',
  `day` tinyint NOT NULL DEFAULT '1',
  `activity` varchar(85) NOT NULL DEFAULT '',
  `starttime` varchar(20) NOT NULL DEFAULT '',
  `endtime` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(4) NOT NULL DEFAULT 'pre',
  `order` tinyint NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `special` (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}