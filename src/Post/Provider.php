<?php


namespace ATD\CruiseFactory\Post;


class Provider {
	public static array $posts = [
		Destination::class,
		CruiseLine::class,
		Ship::class,
		Port::class,
		Departure::class
	];

	public static function registerPosts() {
		/** @var Post $post */
		foreach ( self::$posts as $post ) {
			$post::register();
		}
	}

	public static function unregisterPosts() {
		/** @var class-string<Post> $post */
		foreach ( self::$posts as $post ) {
			unregister_post_type( $post::$postType );
		}
	}

	public static function getPostClassByPostType( string $postType ): ?string {
		if ( $postType = array_filter( self::$posts, function ( $p ) use ( $postType ) {
			return $p::$postType === $postType;
		} ) ) {
			return reset( $postType );
		}

		return null;
	}
}