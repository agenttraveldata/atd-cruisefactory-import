<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;

class CruiseLine extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_cruise_line';
	protected static string $feedName = 'cruiselines';
	protected static string $entity = Entity\CruiseLine::class;
	protected static array $collections = [ 'cruiseline_id' => [ Entity\Ship::class ] ];
	protected array $xmlFieldModifiers = [
		'logodata' => [ self::class, 'convertLogo' ]
	];

	protected static function convertLogo( string $string ): string {
		return base64_decode( $string );
	}

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `location` varchar(200) NOT NULL DEFAULT '',
  `booking_email` varchar(100) DEFAULT NULL,
  `brief_desc` varchar(200) NOT NULL DEFAULT '',
  `company_bio` text NOT NULL,
  `logodata` longblob,
  `logosize` varchar(50) DEFAULT NULL,
  `logotype` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `star_rating` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}