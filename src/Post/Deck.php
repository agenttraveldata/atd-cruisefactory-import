<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use WP_Query;

class Deck {
	private static string $imageUrl = 'https://ik.imagekit.io/atd/ships/deckplans/';

	public static function add( Entity\Deck $details ): ?int {
		$shipPost = new WP_Query( [
			'post_type'      => Ship::$postType,
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_key'       => Feed\Ship::$metaKeyId,
			'meta_value'     => $details->getShip()->getId()
		] );

		if ( $shipPost->post_count !== 1 ) {
			return null;
		}

		$postData = [
			'post_title'   => $details->getName(),
			'post_content' => $details->getLevel(),
			'meta_input'   => [
				Feed\Deck::$metaKeyId => $details->getId(),
				'atd_cfi_ship_id'     => $details->getShip()->getId()
			]
		];

		if ( ! empty( $details->getImage() ) ) {
			self::createAttachment( self::$imageUrl . $details->getImage(), $shipPost->post->ID, $postData );
		}

		return true;
	}

	private static function createAttachment( string $imageUrl, int $postId, array $postData ): void {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$imageFileName = 'atd-cfi_deck-' . wp_basename( $imageUrl ) . ( pathinfo( $imageUrl, PATHINFO_EXTENSION ) === '' ? '.jpg' : '' );
		$originalPost  = new WP_Query( [
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_query'     => [
				'relation' => 'AND',
				'id'       => [
					'key'   => Feed\Deck::$metaKeyId,
					'value' => $postData['meta_input'][ Feed\Deck::$metaKeyId ]
				]
			]
		] );

		if ( $originalPost->post_count === 1 ) {
			if ( defined( 'ATD_CF_XML_IMAGE_OVERWRITE' ) ) {
				$thumb     = wp_get_attachment_url( $originalPost->post->ID );
				$uploadDir = wp_get_upload_dir();
				$subPath   = str_replace( $uploadDir['baseurl'], '', $thumb );
				$thumbPath = $uploadDir['basedir'] . $subPath;
				file_put_contents( $thumbPath, file_get_contents( $imageUrl ) );
			}

			$postData['ID'] = $originalPost->post->ID;
			Logger::modify( sprintf( '[%d] Updated %s post %s', $postData['meta_input'][ Feed\Deck::$metaKeyId ], 'attachment', $originalPost->post->post_title ) );

			// update attachment
			wp_update_post( $postData );

			return;
		}

		$file = [
			'name'     => $imageFileName,
			'tmp_name' => download_url( $imageUrl )
		];

		if ( ! is_wp_error( $file['tmp_name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$id = media_handle_sideload( $file, $postId, $postData['post_title'], $postData );
			Logger::add( sprintf( '[%d] Added %s post %s', $postData['meta_input'][ Feed\Deck::$metaKeyId ], 'attachment', $postData['post_title'] ) );

			if ( is_wp_error( $id ) ) {
				@unlink( $file['tmp_name'] );
			}
		}
	}
}