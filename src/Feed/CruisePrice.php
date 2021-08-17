<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Entity;
use DateTimeInterface;

class CruisePrice extends AbstractFeed {
	protected static string $tableName = 'atd_cfi_cruise_price';
	protected static string $feedName = 'cabincategorypricing';
	protected static string $entity = Entity\CruisePrice::class;
	protected static ?string $forceServiceType = 'services';
	protected array $xmlFieldModifiers = [
		'price_single' => [ self::class, 'convertPrice' ],
		'price_double' => [ self::class, 'convertPrice' ],
		'price_triple' => [ self::class, 'convertPrice' ],
		'price_quad'   => [ self::class, 'convertPrice' ]
	];

	public static function convertPrice( float $value ): ?float {
		if ( $value < 1 ) {
			return null;
		}

		return $value;
	}

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
  `cabin` varchar(48) NOT NULL DEFAULT '',
  `sailingdate_id` int NOT NULL,
  `price_single` decimal(10,2) DEFAULT NULL,
  `price_double` decimal(10,2) DEFAULT NULL,
  `price_triple` decimal(10,2) DEFAULT NULL,
  `price_quad` decimal(10,2) DEFAULT NULL,
  `currency` char(8) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sailingdateid` (`sailingdate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}