<?php


namespace ATD\CruiseFactory\Entity;


use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;

class Cruise {
	public int $id;
	private string $name;
	private string $brief_description;
	private string $description;
	private string $photo;
	private int $length;
	private ?Destination $destination;
	private ?CruiseLine $cruiseLine;
	private ?Ship $ship;
	private ?Port $embarkPort;
	private ?Port $disembarkPort;
	private Collection $itinerary;

	public function __construct() {
		$this->itinerary = new ArrayCollection();
	}

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName( string $name ): self {
		$this->name = $name;

		return $this;
	}

	public function getBriefDescription(): string {
		return $this->brief_description;
	}

	public function setBriefDescription( string $briefDescription ): self {
		$this->brief_description = $briefDescription;

		return $this;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription( string $description ): self {
		$this->description = $description;

		return $this;
	}

	public function getPhoto(): string {
		return $this->photo;
	}

	public function setPhoto( string $photo ): self {
		$this->photo = $photo;

		return $this;
	}

	public function getDuration(): int {
		return $this->length;
	}

	public function setDuration( int $length ): self {
		$this->length = $length;

		return $this;
	}

	public function getDurationTerm(): ?string {
		if ( $this->length ) {
			if ( $this->length < 7 ) {
				return '1-6';
			} elseif ( $this->length < 14 ) {
				return '7-13';
			} elseif ( $this->length < 20 ) {
				return '14-19';
			} else {
				return '20+';
			}
		}

		return null;
	}

	public function getDestination(): ?Destination {
		return $this->destination ?? null;
	}

	public function setDestination( Destination $destination ): self {
		$this->destination = $destination;

		return $this;
	}

	public function getCruiseLine(): ?CruiseLine {
		return $this->cruiseLine ?? null;
	}

	public function setCruiseLine( CruiseLine $cruiseLine ): self {
		$this->cruiseLine = $cruiseLine;

		return $this;
	}

	public function getShip(): ?Ship {
		return $this->ship ?? null;
	}

	public function setShip( Ship $ship ): self {
		$this->ship = $ship;

		return $this;
	}

	public function getEmbarkPort(): ?Port {
		return $this->embarkPort ?? null;
	}

	public function setEmbarkPort( Port $embarkPort ): self {
		$this->embarkPort = $embarkPort;

		return $this;
	}

	public function getDisembarkPort(): ?Port {
		return $this->disembarkPort ?? null;
	}

	public function setDisembarkPort( Port $disembarkPort ): self {
		$this->disembarkPort = $disembarkPort;

		return $this;
	}

	public function getItinerary(): Collection {
		return $this->itinerary;
	}

	public function setItinerary( array $itinerary ): self {
		$this->itinerary = new ArrayCollection( $itinerary );

		return $this;
	}
}