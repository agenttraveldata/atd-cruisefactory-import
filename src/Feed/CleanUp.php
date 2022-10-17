<?php


namespace ATD\CruiseFactory\Feed;


use ATD\CruiseFactory\Post;
use ATD\CruiseFactory\Services\ConvertClass;
use ATD\CruiseFactory\Taxonomy;
use WP_Post;
use WP_Query;
use WP_Term;
use wpdb;

class CleanUp {
	private static string $tableName = 'atd_cfi_cleanup';
	private wpdb $wpdb;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	public function add( string $tableName, array $ids, string $metaKey ): void {
		foreach ( $ids as $id ) {
			$this->wpdb->insert( $this->wpdb->prefix . self::$tableName, [
				'table_name' => $tableName,
				'row_id'     => $id,
				'meta_key'   => $metaKey
			] );
		}
	}

	public function removeWordPressPostAndRelated( string $tableName, int $id, string $metaKey ): void {
		if ( $postType = $this->getPostTypeByTableName( $tableName ) ) {
			if ( $post = $this->fetchPostByTypeAndMetaValue( $postType, $id, $metaKey ) ) {
				$this->wpdb->query( 'DELETE FROM ' . $this->wpdb->postmeta . ' WHERE post_id=' . $post->ID );
				$this->deleteTaxonomyIfPrimaryPost( $post );
				wp_delete_post( $post->ID, true );
			}
		}
	}

	private function deleteTaxonomyIfPrimaryPost( WP_Post $post ): void {
		$term = [];

		if ( ! $metaId = get_post_meta( $post->ID, 'atd_cfi_id', true ) ) {
			return;
		}

		switch ( $post->post_type ) {
			case Post\CruiseLine::$postType:
				$term = get_term_by( 'slug', $metaId, Taxonomy\CruiseLine::$name );
				break;
			case Post\Ship::$postType:
				$term = get_term_by( 'slug', $metaId, Taxonomy\Ship::$name );
				break;
			case Post\Destination::$postType:
				$term = get_term_by( 'slug', $metaId, Taxonomy\Destination::$name );
				break;
		}

		if ( $term instanceof WP_Term ) {
			$term = [ $term ];
		}

		if ( ! empty( $term[0] ) && $term[0] instanceof WP_Term ) {
			wp_delete_term( $term[0]->term_id, $term[0]->taxonomy );
		}
	}

	private function fetchPostByTypeAndMetaValue( string $postType, $metaValue, string $metaKey = 'atd_cfi_id' ): ?WP_Post {
		$post = new WP_Query( [
			'post_type'      => $postType,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'posts_per_page' => 1,
			'meta_query'     => [
				'atd_cfi_id' => [
					'key'     => $metaKey,
					'value'   => $metaValue,
					'compare' => '='
				]
			]
		] );

		if ( $post->post_count > 0 ) {
			return $post->posts[0];
		}

		return null;
	}

	private function getPostTypeByTableName( string $tableName ): ?string {
		foreach ( Provider::getFeeds() as $feed ) {
			if ( $feed::getTableNameWithPrefix() === $tableName ) {
				if ( $postClass = ConvertClass::toPostFromFeed( $feed ) ) {
					if ( class_exists( $postClass ) && property_exists( $postClass, 'postType' ) ) {
						/** @var Post\Post $postClass */
						return $postClass::$postType;
					}
				}
			}
		}

		return null;
	}

	public static function getTableName( string $prefix = '' ): string {
		return $prefix . static::$tableName;
	}

	public static function getTableNameWithPrefix(): string {
		global $wpdb;

		return $wpdb->prefix . self::$tableName;
	}

	public static function getCreateTableSchema(): string {
		$tableName = self::getTableNameWithPrefix();

		return <<<SQL
CREATE TABLE `$tableName` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `row_id` int NOT NULL,
  `meta_key` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
	}
}