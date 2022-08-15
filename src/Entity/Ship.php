<?php


namespace ATD\CruiseFactory\Entity;


use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;

class Ship {
	public int $id;
	private string $name;
	private string $description;
	private string $thumbnail;
	private string $maidenvoyage;
	private string $refurbished;
	private string $tonnage;
	private string $length;
	private string $beam;
	private string $draft;
	private string $speed;
	private string $ship_rego;
	private string $pass_capacity;
	private string $pass_space;
	private string $crew_size;
	private string $nat_crew;
	private string $nat_officers;
	private string $nat_dining;
	private ?CruiseLine $cruiseLine;
	private Collection $cabins;
	private Collection $amenities;
	private Collection $facilities;

	public function __construct() {
		$this->cabins  = new ArrayCollection();
		$this->amenities  = new ArrayCollection();
		$this->facilities = new ArrayCollection();
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

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription( string $description ): self {
		$this->description = $description;

		return $this;
	}

	public function getThumbnail(): string {
		return $this->thumbnail;
	}

	public function setThumbnail( string $thumbnail ): self {
		$this->thumbnail = $thumbnail;

		return $this;
	}

	public function getMaidenVoyage(): string {
		return $this->maidenvoyage;
	}

	public function setMaidenVoyage( string $maidenVoyage ): self {
		$this->maidenvoyage = $maidenVoyage;

		return $this;
	}

	public function getCruiseLine(): ?CruiseLine {
		return $this->cruiseLine ?? null;
	}

	public function setCruiseLine( CruiseLine $cruiseLine ): self {
		$this->cruiseLine = $cruiseLine;

		return $this;
	}

	public function getCabins(): Collection {
		return $this->cabins;
	}

	public function setCabins( array $cabins ): self {
		$this->cabins = new ArrayCollection( $cabins );

		return $this;
	}

	public function getAmenity(): Collection {
		return $this->amenities;
	}

	public function setAmenity( array $amenities ): self {
		$this->amenities = new ArrayCollection( $amenities );

		return $this;
	}

	public function getFacility(): Collection {
		return $this->facilities;
	}

	public function setFacility( array $facilities ): self {
		$this->facilities = new ArrayCollection( $facilities );

		return $this;
	}

	public function getRefurbished(): string {
		return $this->refurbished;
	}

	public function setRefurbished( string $refurbished ): self {
		$this->refurbished = $refurbished;

		return $this;
	}

	public function getTonnage(): string {
		return $this->tonnage;
	}

	public function setTonnage( string $tonnage ): self {
		$this->tonnage = $tonnage;

		return $this;
	}

	public function getLength(): string {
		return $this->length;
	}

	public function setLength( string $length ): self {
		$this->length = $length;

		return $this;
	}

	public function getBeam(): string {
		return $this->beam;
	}

	public function setBeam( string $beam ): self {
		$this->beam = $beam;

		return $this;
	}

	public function getDraft(): string {
		return $this->draft;
	}

	public function setDraft( string $draft ): self {
		$this->draft = $draft;

		return $this;
	}

	public function getSpeed(): string {
		return $this->speed;
	}

	public function setSpeed( string $speed ): self {
		$this->speed = $speed;

		return $this;
	}

	public function getShipRego(): string {
		return $this->ship_rego;
	}

	public function setShipRego( string $shipRego ): self {
		$this->ship_rego = $shipRego;

		return $this;
	}

	public function getPassCapacity(): string {
		return $this->pass_capacity;
	}

	public function setPassCapacity( string $passCapacity ): self {
		$this->pass_capacity = $passCapacity;

		return $this;
	}

	public function getPassSpace(): string {
		return $this->pass_space;
	}

	public function setPassSpace( string $passSpace ): self {
		$this->pass_space = $passSpace;

		return $this;
	}

	public function getCrewSize(): string {
		return $this->crew_size;
	}

	public function setCrewSize( string $crewSize ): self {
		$this->crew_size = $crewSize;

		return $this;
	}

	public function getNatCrew(): string {
		return $this->nat_crew;
	}

	public function setNatCrew( string $natCrew ): self {
		$this->nat_crew = $natCrew;

		return $this;
	}

	public function getNatOfficers(): string {
		return $this->nat_officers;
	}

	public function setNatOfficers( string $natOfficers ): self {
		$this->nat_officers = $natOfficers;

		return $this;
	}

	public function getNatDining(): string {
		return $this->nat_dining;
	}

	public function setNatDining( string $natDining ): self {
		$this->nat_dining = $natDining;

		return $this;
	}
}