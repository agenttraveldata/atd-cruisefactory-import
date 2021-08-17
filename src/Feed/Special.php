<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class Special extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_special';
	protected static string $feedName = 'specials';
	protected static string $entity = Entity\Special::class;
	public array $dependencies = [
		Cruise::class,
		SpecialLeadPrice::class,
		SpecialPrice::class,
		SpecialItinerary::class
	];
	protected array $collections = [
		'special_id' => [
			Entity\SpecialPrice::class,
			Entity\SpecialItinerary::class
		]
	];
	protected array $relationships = [
		'currency_id'                  => Entity\Currency::class,
		Entity\SpecialLeadPrice::class => 'special_id'
	];

	public function import( DateTimeInterface $updatedAt ): bool {
		return true;
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
  `cruise_id` int NOT NULL DEFAULT '0',
  `factory_id` int NOT NULL DEFAULT '0',
  `priority_id` int NOT NULL DEFAULT '1',
  `special_header` varchar(100) DEFAULT NULL,
  `special_text` longtext,
  `special_brief` longtext,
  `instructions` longtext,
  `booking_email` varchar(100) DEFAULT NULL,
  `start_price` decimal(9,2) DEFAULT NULL,
  `currency_id` int NOT NULL DEFAULT '1',
  `cruise_order` int NOT NULL DEFAULT '0',
  `main_special` char(1) DEFAULT 'n',
  `main_wave` char(1) DEFAULT 'n',
  `dest_special` char(1) DEFAULT 'n',
  `special_order` int DEFAULT NULL,
  `validity_date_end` date DEFAULT NULL,
  `validity_date_start` date DEFAULT NULL,
  `checked` char(1) DEFAULT 'n',
  `internal_notes` text,
  `advert_code` varchar(255) DEFAULT NULL,
  `exchange_rate` double(11,4) DEFAULT NULL,
  `ex_rate_date` date DEFAULT NULL,
  `currency_id_ref` int unsigned NOT NULL DEFAULT '0',
  `create_pdf` char(1) DEFAULT 'n',
  `uploaded_pdf` varchar(255) DEFAULT NULL,
  `withdrawn` char(1) NOT NULL DEFAULT 'n',
  `quicksave` char(1) DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `type` varchar(48) NOT NULL DEFAULT 'Cruise Only',
  `escorted` char(1) DEFAULT 'n',
  `wedding` char(1) DEFAULT 'n',
  `agentonly` char(1) DEFAULT 'n',
  `special_conditions` text,
  `specialpdf_filename` varchar(255) DEFAULT NULL,
  `specialpdf_contents` text,
  `seniors` enum('Yes','No') DEFAULT 'No',
  `singles` enum('Yes','No') DEFAULT 'No',
  `ship_id` int DEFAULT '0',
  `cruiseline_id` int DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cruise_id` (`cruise_id`),
  KEY `cruiseline_id` (`cruiseline_id`),
  KEY `ship_id` (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}