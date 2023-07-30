<?php


namespace ATD\CruiseFactory\Services\Data\DBAL;


use ATD\CruiseFactory\Feed\Feed;
use ATD\CruiseFactory\Services\ConvertClass;
use Exception;
use mysqli;
use mysqli_stmt;

class DataMapper {
	private string $tableName;
	private mysqli $dbh;
	private string $entity;
	private int $relationshipDepth = 0;

	public function __construct( mysqli $dbh ) {
		$this->dbh = $dbh;
	}

	/**
	 * @param string $entity
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setEntity( string $entity ): self {
		if ( str_contains( $entity, '\Entity\\' ) ) {
			$feedClass = ConvertClass::toFeedFromEntity( $entity );

			if ( class_exists( $feedClass ) ) {
				/** @var Feed $feedClass */
				$this->tableName = $feedClass::getTableNameWithPrefix();
				$this->entity    = $entity;

				return $this;
			}
		}

		throw new Exception( "Unable to find feed class for entity $entity!" );
	}

	/**
	 * @param int $id
	 *
	 * @return object|null
	 */
	public function find( int $id ): ?object {
		try {
			$stmt = $this->getPreparedStatement( [ 'i.id=' => $id ] );
		} catch ( Exception ) {
			return null;
		}

		$stmt->execute();
		$res = $stmt->get_result();
		if ( $row = $res->fetch_object( $this->entity ) ) {
			$this->defineRelationships( [ $row ] );

			return $row;
		}

		return null;
	}

	/**
	 * @param array $where
	 * @param int|null $limit
	 *
	 * @return array
	 */
	public function findBy( array $where, ?int $limit = null ): array {
		$rows = [];

		try {
			$stmt = $this->getPreparedStatement( $where, $limit );
		} catch ( Exception ) {
			return $rows;
		}

		$stmt->execute();
		$res = $stmt->get_result();

		while ( $row = $res->fetch_object( $this->entity ) ) {
			$rows[] = $row;
		}

		if ( ! empty( $rows ) ) {
			$this->defineRelationships( $rows );
		}

		return $rows;
	}

	/**
	 * @param array $rows
	 */
	private function defineRelationships( array $rows ): void {
		if ( ! $firstRow = reset( $rows ) ) {
			return;
		}

		/** @var class-string<Feed> $parentFeed */
		$parentFeed = ConvertClass::toFeedFromEntity( $firstRow );

		if ( ! class_exists( $parentFeed ) ) {
			return;
		}

		foreach ( $parentFeed::getRelationships() as $column => $entityClass ) {
			$relationColumn = 'id';
			$propertyName   = str_replace( '_id', '', $column );

			if ( class_exists( $column ) ) {
				$relationColumn = $entityClass;
				$entityClass    = $column;
				$column         = 'id';
				$propertyName   = substr( strrchr( $entityClass, '\\' ), 1 );
			}

			if ( property_exists( $firstRow, $column ) ) {
				/** @var class-string<Feed> $feedClass */
				$feedClass = ConvertClass::toFeedFromEntity( $entityClass );

				/*
				 * we can force id here because we know the queries/columns, etc
				 */
				$res = $this->dbh->query(
					'SELECT * ' .
					' FROM ' . $feedClass::getTableNameWithPrefix() .
					' WHERE ' . $relationColumn . ' IN (' . implode( ',', array_unique( array_column( $rows, $column ) ) ) . ')'
				);

				$relations = [];
				while ( $relation = $res->fetch_object( $entityClass ) ) {
					$relations[] = $relation;
				}

				if ( count( $relations ) > 0 && $this->relationshipDepth <= 2 && ( count( $feedClass::getRelationships() ) > 0 ) || count( $feedClass::getCollections() ) > 0 ) {
					$this->relationshipDepth ++;
					$this->defineRelationships( $relations );
					$this->relationshipDepth --;
				}

				foreach ( $rows as $row ) {
					if ( false !== ( $key = array_search( $row->{$column}, array_column( $relations, $relationColumn ) ) ) ) {
						$this->setPropertyValueOfEntity( $row, $propertyName, $relations[ $key ] );
					}
				}
			}
		}

		if ( ! defined( 'WP_IMPORTING' ) ) {
			/*
			 * We don't need this during import
			 */
			if ( ! empty( $parentFeed::getCollections() ) ) {
				$ids = array_unique( array_column( $rows, 'id' ) );
				sort( $ids );

				foreach ( $parentFeed::getCollections() as $column => $entities ) {
					foreach ( $entities as $entity ) {
						$entityName   = substr( strrchr( $entity, '\\' ), 1 );
						$parentSetter = 'set' . $entityName . ( str_ends_with( $entityName, 'y' ) ? '' : 's' );

						if ( method_exists( ConvertClass::toEntityFromFeed( $parentFeed ), $parentSetter ) ) {
							/** @var Feed $entityFeed */
							$entityFeed = ConvertClass::toFeedFromEntity( $entity );

							$res = $this->dbh->query(
								'SELECT * ' .
								' FROM ' . $entityFeed::getTableNameWithPrefix() .
								' WHERE ' . $column . ' IN (' . implode( ',', $ids ) . ')' .
								' ORDER BY ' . $column . ' ASC'
							);

							$id         = 0;
							$collection = [];
							while ( $relation = $res->fetch_object( $entity ) ) {
								if ( $id !== $relation->{$column} ) {
									if ( ! empty( $collection ) ) {
										$this->defineRelationships( $collection );
										$rows[ array_search( (int) $id, $ids ) ]->{$parentSetter}( $collection );
										$collection = [];
									}
								}

								$collection[] = $relation;

								$id = $relation->{$column};
								unset( $relation->{$column} );
							}

							if ( ! empty( $collection ) ) {
								$this->defineRelationships( $collection );
								$rows[ array_search( (int) $id, $ids ) ]->{$parentSetter}( $collection );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param object $entity
	 * @param string $propertyName
	 * @param $value
	 */
	private function setPropertyValueOfEntity( object $entity, string $propertyName, $value ): void {
		$propertyMethod = 'set' . str_replace( '_', '', ucwords( $propertyName, '_' ) );
		if ( method_exists( $entity, $propertyMethod ) ) {
			$entity->{$propertyMethod}( $value );
		}
	}

	/**
	 * @param $where
	 * @param int|null $chunkCounter
	 * @param int $chunkSize
	 *
	 * @return mysqli_stmt
	 * @throws Exception
	 */
	private function getPreparedStatement( $where, ?int $chunkCounter = null, int $chunkSize = ATD_CF_XML_CHUNK_LIMIT ): mysqli_stmt {
		$select = [ 'i.*' ];
		$from   = [ $this->tableName . ' i' ];

		$whereKeys   = '';
		$whereValues = [];
		if ( ! empty( $where ) ) {
			foreach ( $where as $column => $value ) {
				switch ( gettype( $value ) ) {
					case 'array':
						$whereKeys .= $column . ' IN (' . implode( ',', array_fill( 0, count( $value ), '%s' ) ) . ')';
						array_push( $whereValues, ...$value );
						break;
					case 'integer':
						$whereKeys     .= $column . ' %d';
						$whereValues[] = (int) $value;
						break;
					default:
						$whereKeys     .= $column . ' %s';
						$whereValues[] = (string) $value;
				}
			}
		}

		if ( $chunkCounter !== null ) {
			$limit = ' LIMIT ' . $chunkCounter . ', ' . $chunkSize;
		}

		if ( ! $stmt = $this->dbh->prepare(
			'SELECT ' . implode( ', ', $select ) .
			' FROM ' . implode( ', ', $from ) .
			' WHERE ' . preg_replace( '/%\w/', '?', $whereKeys ) . ( $limit ?? '' )
		) ) {
			throw new Exception( "Unable to prepare query statement! {$this->dbh->error}" );
		}

		if ( preg_match_all( '/%(\w)/', $whereKeys, $matches ) ) {
			$bindMatches = [];

			foreach ( $matches[1] as $key => $match ) {
				$bindMatches[ $match ][] = $whereValues[ $key ];
			}

			foreach ( $bindMatches as $type => $value ) {
				$stmt->bind_param( str_repeat( $type, count( $value ) ), ...$value );
			}
		}

		return $stmt;
	}
}