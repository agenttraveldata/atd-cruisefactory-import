<?php


namespace ATD\CruiseFactory\Services\WordPress\Blocks;


use WP_Block_Parser_Block;

class Block {
	public string $blockName;
	public array $attributes = [];
	public string $innerHTML = '';
	public array $innerContent = [];
	public array $innerBlocks = [];

	public function __construct( ?string $html = null ) {
		if ( $html ) {
			$this->setInnerHTML( $html );
			$this->setInnerContent( [ $html ] );
		}
	}

	public function getBlockName(): string {
		return $this->blockName;
	}

	public function setBlockName( string $blockName ): self {
		$this->blockName = $blockName;

		return $this;
	}

	public function getAttributes(): array {
		return $this->attributes;
	}

	public function setAttributes( array $attributes ): self {
		$this->attributes = $attributes;

		return $this;
	}

	public function getInnerHTML(): string {
		return $this->innerHTML;
	}

	public function setInnerHTML( string $innerHTML ): self {
		$this->innerHTML = $innerHTML;

		return $this;
	}

	public function getInnerContent(): array {
		return $this->innerContent;
	}

	public function setInnerContent( array $innerContent ): self {
		$this->innerContent = $innerContent;

		return $this;
	}

	public function getInnerBlocks(): array {
		return $this->innerBlocks;
	}

	public function setInnerBlocks( array $innerBlocks ): self {
		$this->innerBlocks = $innerBlocks;

		return $this;
	}

	public function render(): string {
		$parserBlock = (array) ( new WP_Block_Parser_Block(
			$this->getBlockName(),
			$this->getAttributes(),
			$this->getInnerBlocks(),
			$this->getInnerHTML(),
			$this->getInnerContent()
		) );

		/** @var $parserBlock WP_Block_Parser_Block */
		return serialize_block( $parserBlock );
	}
}