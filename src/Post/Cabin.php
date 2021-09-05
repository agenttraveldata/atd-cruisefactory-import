<?php


namespace ATD\CruiseFactory\Post;


use ATD\CruiseFactory\Entity;
use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Services\Logger;
use WP_Query;

class Cabin {
	private static string $cabinImageUrl = 'https://ik.imagekit.io/atd/ships/cabins/';
	private static string $cabinPhotoUrl = 'https://ik.imagekit.io/atd/ships/cabinphotos/';

	public static function add( Entity\Cabin $details ): ?int {
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
			'post_content' => $details->getDescription(),
			'post_status'  => 'publish',
			'meta_input'   => [
				Feed\Cabin::$metaKeyId => $details->getId(),
				'atd_cfi_ship_id'      => $details->getShip()->getId(),
				'atd_cfi_cabin_order'  => $details->getOrder()
			]
		];

		if ( ! empty( $details->getImage() ) ) {
			$postData['meta_input']['atd_cfi_cabin_image_type'] = 'image';
			self::createAttachment( self::$cabinImageUrl . $details->getImage(), $shipPost->post->ID, $postData );
		}

		if ( ! empty( $details->getPhoto() ) ) {
			$postData['meta_input']['atd_cfi_cabin_image_type'] = 'photo';
			self::createAttachment( self::$cabinPhotoUrl . $details->getPhoto(), $shipPost->post->ID, $postData );
		}

		return true;
	}

	private static function createAttachment( string $imageUrl, int $postId, array $postData ): void {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$imageFileName = 'atd-cfi_' . wp_basename( $imageUrl ) . ( pathinfo( $imageUrl, PATHINFO_EXTENSION ) === '' ? '.jpg' : '' );
		$originalPost  = new WP_Query( [
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'nopaging'       => true,
			'no_found_rows'  => true,
			'meta_query'     => [
				'relation' => 'AND',
				'id'       => [
					'key'   => Feed\Cabin::$metaKeyId,
					'value' => $postData['meta_input'][ Feed\Cabin::$metaKeyId ]
				],
				'type'     => [
					'key'   => 'atd_cfi_cabin_image_type',
					'value' => $postData['meta_input']['atd_cfi_cabin_image_type']
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
			Logger::modify( sprintf( '[%d] Updated %s post %s', $postData['meta_input'][ Feed\Cabin::$metaKeyId ], 'attachment', $originalPost->post->post_title ) );

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
			Logger::modify( sprintf( '[%d] Added %s post %s', $postData['meta_input'][ Feed\Cabin::$metaKeyId ], 'attachment', $postData['post_title'] ) );

			if ( is_wp_error( $id ) ) {
				@unlink( $file['tmp_name'] );
			}
		}
	}
}