<?php

namespace ATD\CruiseFactory\Services;

class ConvertClass {
	/**
	 * @param string|object $feedClassName
	 *
	 * @return string|null
	 */
	public static function toPostFromFeed( $feedClassName ): ?string {
		if ( is_object( $feedClassName ) ) {
			$feedClassName = get_class( $feedClassName );
		}

		$postClassName = str_replace( '\Feed\\', '\Post\\', $feedClassName );
		if ( class_exists( $postClassName ) ) {
			return $postClassName;
		}

		return null;
	}

	/**
	 * @param string|object $entityClassName
	 *
	 * @return string|null
	 */
	public static function toFeedFromEntity( $entityClassName ): ?string {
		if ( is_object( $entityClassName ) ) {
			$entityClassName = get_class( $entityClassName );
		}

		$feedClassName = str_replace( '\Entity\\', '\Feed\\', $entityClassName );
		if ( class_exists( $feedClassName ) ) {
			return $feedClassName;
		}

		return null;
	}

	/**
	 * @param string|object $postClassName
	 *
	 * @return string|null
	 */
	public static function toEntityFromPost( $postClassName ): ?string {
		if ( is_object( $postClassName ) ) {
			$postClassName = get_class( $postClassName );
		}

		$entityClassName = str_replace( '\Post\\', '\Entity\\', $postClassName );
		if ( class_exists( $entityClassName ) ) {
			return $entityClassName;
		}

		return null;
	}

	/**
	 * @param string|object $feedClassName
	 *
	 * @return string|null
	 */
	public static function toEntityFromFeed( $feedClassName ): ?string {
		if ( is_object( $feedClassName ) ) {
			$feedClassName = get_class( $feedClassName );
		}

		$entityClassName = str_replace( '\Feed\\', '\Entity\\', $feedClassName );
		if ( class_exists( $entityClassName ) ) {
			return $entityClassName;
		}

		return null;
	}

	/**
	 * @param string|object $postClassName
	 *
	 * @return string|null
	 */
	public static function toFeedFromPost( $postClassName ): ?string {
		if ( is_object( $postClassName ) ) {
			$postClassName = get_class( $postClassName );
		}

		$feedClassName = str_replace( '\Post\\', '\Feed\\', $postClassName );
		if ( class_exists( $feedClassName ) ) {
			return $feedClassName;
		}

		return null;
	}
}