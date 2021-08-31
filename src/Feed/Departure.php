<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;

class Departure extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_departure';
	protected static string $feedName = 'sailingdates';
	protected static string $entity = Entity\Departure::class;
	public array $dependencies = [ Cruise::class ];
	protected static array $relationships = [ 'cruise_id' => Entity\Cruise::class ];
	protected static array $collections = [ 'sailingdate_id' => [ Entity\CruisePrice::class ] ];

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cruise_id` int NOT NULL DEFAULT '0',
  `sailingdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `embarkport_id` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cruise_id` (`cruise_id`),
  KEY `sailingdate` (`embarkport_id`),
  KEY `cruise_id_sailingdate` (`cruise_id`,`sailingdate`),
  KEY `sailingdate_only` (`sailingdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}