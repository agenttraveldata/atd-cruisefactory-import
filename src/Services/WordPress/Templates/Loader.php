<?php


namespace ATD\CruiseFactory\Services\WordPress\Templates;


use ATD\CruiseFactory\Post;

class Loader {
	public function templateLoader( $defaultTemplate ) {
		if ( is_embed() ) {
			return $defaultTemplate;
		}

		if ( $template = $this->getPluginTemplateName() ) {
			if ( $templateFilePath = atd_cfi_locate_template( [ $template ] ) ) {
				return $templateFilePath;
			}
		}

		return $defaultTemplate;
	}

	private function getPluginTemplateName(): ?string {
		$postType = array_filter( Post\Provider::$posts, function ( $p ) {
			/** @var Post\Post $p */
			return $p::$postType === get_post_type();
		} );

		if ( ! empty( $postType ) ) {
			$postType = reset( $postType );

			if ( is_archive() ) {
				return 'archive-' . $postType::$postType . '.php';
			} elseif ( is_single() ) {
				return 'single-' . $postType::$postType . '.php';
			}
		}

		return null;
	}
}