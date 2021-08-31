<?php

namespace Tests;

use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Services\Data\DBAL\DataMapper;
use PHPUnit\Framework\TestCase;
use stdClass;

class DataMapperTest extends TestCase {
	public static function setUpBeforeClass(): void {
		define( 'ATD_CF_XML_CHUNK_LIMIT', 500 );

		$wpdb         = new stdClass();
		$wpdb->prefix = 'wp_';

		$GLOBALS['wpdb'] = $wpdb;
	}

	public function testInvalidEntityClassUsed(): void {
		$mockMysqli = $this->createMock( 'mysqli' );
		$dataMapper = new DataMapper( $mockMysqli );

		$this->expectException( \Exception::class );
		$dataMapper->setEntity( stdClass::class );
	}

	public function testFindReturnsSingleObject(): void {
		$port    = ( new Entity\Port() )->setId( 1 )->setName( 'Test Port' );
		$latLong = ( new Entity\LatLong() )->setId( 1 );

		$mockMysqli = $this->createMock( 'mysqli' );
		$mockStmt   = $this->createMock( 'mysqli_stmt' );
		$mockResult = $this->createMock( 'mysqli_result' );

		$mockResult->method( 'fetch_object' )->willReturn( $port, $latLong );
		$mockStmt->method( 'get_result' )->willReturn( $mockResult );
		$mockMysqli->method( 'prepare' )->willReturn( $mockStmt );
		$mockMysqli->method( 'query' )->willReturn( $mockResult );

		$this->assertIsObject( ( new DataMapper( $mockMysqli ) )->setEntity( Entity\Port::class )->find( 1 ) );
	}

	public function testFindByReturnsArrayOfObjects(): void {
		$port    = ( new Entity\Port() )->setId( 1 )->setName( 'Test Port' );
		$latLong = new Entity\LatLong();

		$latLong->setId( 1 );
		$latLong->port_id = $port->getId();

		$mockMysqli = $this->createMock( 'mysqli' );
		$mockStmt   = $this->createMock( 'mysqli_stmt' );
		$mockResult = $this->createMock( 'mysqli_result' );

		$mockResult->method( 'fetch_object' )->willReturn( $port, $latLong );
		$mockStmt->method( 'get_result' )->willReturn( $mockResult );
		$mockMysqli->method( 'prepare' )->willReturn( $mockStmt );
		$mockMysqli->method( 'query' )->willReturn( $mockResult );

		$this->assertIsArray( ( new DataMapper( $mockMysqli ) )->setEntity( Entity\Port::class )->findBy( [ 'id =' => 1 ] ) );
	}
}