<?php

namespace ATD\CruiseFactory\Entity;

class CruisePrice {
	public int $id;
	private string $cabin_name;
	private ?Cabin $cabin;
	private ?float $price_single;
	private ?float $price_double;
	private ?float $price_triple;
	private ?float $price_quad;
	private string $currency;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getCabinName(): string {
		return $this->cabin_name;
	}

	public function setCabinName( string $cabinName ): self {
		$this->cabin_name = $cabinName;

		return $this;
	}

	public function getCabin(): ?Cabin {
		return $this->cabin ?? null;
	}

	public function setCabin( Cabin $cabin ): self {
		$this->cabin = $cabin;

		return $this;
	}

	public function getPriceSingle(): ?float {
		return $this->price_single ?? null;
	}

	public function setPriceSingle( float $priceSingle ): self {
		$this->price_single = $priceSingle;

		return $this;
	}

	public function getPrice(): ?float {
		return ( $this->isSinglePrice() ? $this->price_single : $this->price_double ) ?? null;
	}

	public function isSinglePrice(): bool {
		return empty( $this->price_double ) && empty( $this->price_triple )
		       && empty( $this->price_quad ) && ! empty( $this->price_single );
	}

	public function getPriceDouble(): ?float {
		return $this->price_double ?? null;
	}

	public function setPriceDouble( float $priceDouble ): self {
		$this->price_double = $priceDouble;

		return $this;
	}

	public function getPriceTriple(): ?float {
		return $this->price_triple ?? null;
	}

	public function setPriceTriple( float $priceTriple ): self {
		$this->price_triple = $priceTriple;

		return $this;
	}

	public function getPriceQuad(): ?float {
		return $this->price_quad ?? null;
	}

	public function setPriceQuad( float $priceQuad ): self {
		$this->price_quad = $priceQuad;

		return $this;
	}

	public function getCurrency(): string {
		return $this->currency;
	}

	public function setCurrency( string $currency ): self {
		$this->currency = $currency;

		return $this;
	}
}