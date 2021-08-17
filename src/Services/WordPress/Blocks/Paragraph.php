<?php


namespace ATD\CruiseFactory\Services\WordPress\Blocks;


class Paragraph extends Block {
	public function __construct( ?string $html = null ) {
		$this->setBlockName( 'core/paragraph' );
		parent::__construct( $html );
	}
}