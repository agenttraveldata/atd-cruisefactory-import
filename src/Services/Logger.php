<?php

namespace ATD\CruiseFactory\Services;

use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;
use DateTime;

class Logger {
	private static Collection $logs;
	/** @var resource $fh */
	private static $fh = null;
	private static array $typeColours = [
		'add'    => [ '#1d5f13', '#e8efe7' ],
		'modify' => [ '#908a1d', '#f4f3e8' ],
		'remove' => [ '#90251d', '#f4e9e8' ],
		'info'   => [ '#7e7e7e', '#f2f2f2' ],
		'error'  => [ '#cc1316', '#fae7e8' ]
	];
	private static array $typeIcons = [
		'add'    => '+',
		'modify' => '~',
		'remove' => '-',
		'info'   => 'i',
		'error'  => 'x'
	];

	public static function add( string $message ) {
		self::createLog( 'add', '[' . self::$typeIcons['add'] . '] ' . $message );
	}

	public static function modify( string $message ) {
		self::createLog( 'modify', '[' . self::$typeIcons['modify'] . '] ' . $message );
	}

	public static function remove( string $message ) {
		self::createLog( 'remove', '[' . self::$typeIcons['remove'] . '] ' . $message );
	}

	public static function info( string $message ) {
		self::createLog( 'info', '[' . self::$typeIcons['info'] . '] ' . $message );
	}

	public static function error( string $message ) {
		self::createLog( 'error', '[' . self::$typeIcons['error'] . '] ' . $message );
	}

	public static function size(): int {
		return isset( self::$logs ) ? self::$logs->count() : 0;
	}

	private static function createLog( string $type, string $message ): void {
		if ( ! defined( 'ATD_CF_XML_LOG_UPDATES' ) ) {
			return;
		}

		self::addLog( ( new Entity\Log() )->setDateTime( new DateTime() )->setType( $type )->setMessage( $message ) );
	}

	private static function addLog( Entity\Log $log ): void {
		if ( ! isset( self::$logs ) ) {
			self::$logs = new ArrayCollection();
		}

		self::$logs->add( $log );

		if ( self::$logs->count() > 0 ) {
			self::flush();
		}
	}

	public static function flush( bool $newLog = false ): void {
		if ( empty( self::$fh ) || $newLog ) {
			if ( ! empty( self::$fh ) ) {
				unlink( stream_get_meta_data( self::$fh )['uri'] );
			}

			self::$fh = fopen( tempnam( dirname( ATD_CF_PLUGIN_FILE ) . '/var/logs', 'import' ), 'w' );
		}

		/** @var Entity\Log $log */
		foreach ( self::$logs as $key => $log ) {
			fwrite( self::$fh, sprintf( '<div style="color: %s;background-color: %s;padding: 4px;margin: 1px 0;">%s - %s</div>' . PHP_EOL,
				self::$typeColours[ $log->getType() ][0],
				self::$typeColours[ $log->getType() ][1],
				$log->getDateTime()->format( ATD_CF_XML_DATE_FORMAT ),
				$log->getMessage()
			) );

			self::$logs->remove( $key );
		}
	}

	public static function emailLogs(): void {
		if ( empty( self::$fh ) ) {
			return;
		}

		if ( self::$logs->count() > 0 ) {
			self::flush();
		}

		$logFileName = stream_get_meta_data( self::$fh )['uri'];

		$to      = get_option( 'admin_email' );
		$subject = sprintf( '%s import of CF XML complete - %s', get_bloginfo( 'name' ), ( new DateTime() )->format( 'd/m/Y H:i:s' ) );
		$body    = file_get_contents( $logFileName );

		if ( ! empty( $body ) ) {
			wp_mail( $to, $subject, $body, [ 'Content-Type: text/html; charset=UTF-8' ] );
		}

		self::destroy();
	}

	public static function destroy() {
		if ( empty( self::$fh ) ) {
			return;
		}

		$logFile = stream_get_meta_data( self::$fh );
		fclose( self::$fh );

		self::$fh = null;

		if ( ! empty( $logFile['uri'] ) ) {
			unlink( $logFile['uri'] );
		}
	}
}