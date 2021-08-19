<?php


namespace ATD\CruiseFactory\Services\WordPress\Blocks;


class Shortcode extends Block {
	public function __construct( ?string $html = null ) {
		$this->setBlockName( 'core/shortcode' );
		parent::__construct( $html );
	}
}