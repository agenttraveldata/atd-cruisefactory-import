<?php


namespace ATD\CruiseFactory\Entity;


class SpecialPrice {
	public int $id;
	private ?Cabin $cabin;
	private float $price;
	private ?Currency $currency;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getCabin(): ?Cabin {
		return $this->cabin ?? null;
	}

	public function setCabin( Cabin $cabin ): self {
		$this->cabin = $cabin;

		return $this;
	}

	public function getPrice(): ?float {
		return $this->price ?? null;
	}

	public function setPrice( float $price ): self {
		$this->price = $price;

		return $this;
	}

	public function getCurrency(): ?Currency {
		return $this->currency;
	}

	public function setCurrency( Currency $currency ): self {
		$this->currency = $currency;

		return $this;
	}
}