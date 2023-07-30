<?php


namespace ATD\CruiseFactory\Entity;


use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;
use DateTime;
use DateTimeInterface;

class Departure {
	public int $id;
	private string $sailingdate;
	private ?float $start_price;
	private ?Cruise $cruise;
	private Collection $cruisePrices;

	public function __construct() {
		$this->cruisePrices = new ArrayCollection();
	}

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getStartPrice(): ?float {
		return $this->start_price ?? null;
	}

	public function setStartPrice( float $startPrice ): self {
		$this->start_price = $startPrice;

		return $this;
	}

	public function getCruise(): ?Cruise {
		return $this->cruise ?? null;
	}

	public function setCruise( Cruise $cruise ): self {
		$this->cruise = $cruise;

		return $this;
	}

	public function getSailingDate(): DateTimeInterface {
		return DateTime::createFromFormat( 'Y-m-d H:i:s', $this->sailingdate );
	}

	public function setSailingDate( DateTimeInterface $sailingDate ): self {
		$this->sailingdate = $sailingDate->format( 'Y-m-d H:i:s' );

		return $this;
	}

	public function getCruisePrices(): Collection {
		return $this->cruisePrices;
	}

	public function setCruisePrices( array $cruisePrices ): self {
		usort( $cruisePrices, function ( $a, $b ) {
			return $a->getPriceDouble() <=> $b->getPriceDouble();
		} );

		$this->cruisePrices = new ArrayCollection( $cruisePrices );

		return $this;
	}
}