<?php


namespace ATD\CruiseFactory\Entity;


class SpecialLeadPrice {
	public int $id;
	private ?float $price_inside;
	private ?float $price_outside;
	private ?float $price_balcony;
	private ?float $price_suites;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getPriceSuite(): ?float {
		return $this->price_suites ?? null;
	}

	public function setPriceSuite( float $priceSuite ): self {
		$this->price_suites = $priceSuite;

		return $this;
	}

	public function getPriceBalcony(): ?float {
		return $this->price_balcony ?? null;
	}

	public function setPriceBalcony( float $priceBalcony ): self {
		$this->price_balcony = $priceBalcony;

		return $this;
	}

	public function getPriceOutside(): ?float {
		return $this->price_outside ?? null;
	}

	public function setPriceOutside( float $priceOutside ): self {
		$this->price_outside = $priceOutside;

		return $this;
	}

	public function getPriceInside(): ?float {
		return $this->price_inside ?? null;
	}

	public function setPriceInside( float $priceInside ): self {
		$this->price_inside = $priceInside;

		return $this;
	}
}