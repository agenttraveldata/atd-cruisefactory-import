<?php


namespace ATD\CruiseFactory\Services\Data\DBAL;


use Exception;
use mysqli;

class EntityManager {
	private DataMapper $mapper;
	private array $loadedMappers = [];

	public function __construct( mysqli $dbh ) {
		$this->mapper = new DataMapper( $dbh );
	}

	/**
	 * @param string $entity
	 *
	 * @return DataMapper
	 * @throws Exception
	 */
	public function getMapper( string $entity ): DataMapper {
		if ( class_exists( $entity ) ) {
			if ( empty( $this->loadedMappers[ $entity ] ) ) {
				$mapper = clone $this->mapper;

				$this->loadedMappers[ $entity ] = $mapper->setEntity( $entity );
			}

			return $this->loadedMappers[ $entity ];
		}

		throw new Exception( sprintf( 'Entity %s not found!', $entity ) );
	}
}