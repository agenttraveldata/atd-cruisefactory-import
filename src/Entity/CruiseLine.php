<?php


namespace ATD\CruiseFactory\Entity;


use ATD\CruiseFactory\Services\Data\Collections\ArrayCollection;
use ATD\CruiseFactory\Services\Data\Collections\Collection;

class CruiseLine {
	public int $id;
	private string $name;
	private string $brief_desc;
	private string $company_bio;
	private string $logodata;
	private string $logotype;
	private Collection $ships;

	public function __construct() {
		$this->ships = new ArrayCollection();
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
		return $this->brief_desc;
	}

	public function setBriefDescription( string $briefDescription ): self {
		$this->brief_desc = $briefDescription;

		return $this;
	}

	public function getDescription(): string {
		return $this->company_bio;
	}

	public function setDescription( string $description ): self {
		$this->company_bio = $description;

		return $this;
	}

	public function getLogoData(): string {
		return $this->logodata;
	}

	public function getLogoType(): string {
		return $this->logotype;
	}

	public function setLogoType( string $logoType ): self {
		$this->logotype = $logoType;

		return $this;
	}

	public function setLogoData( string $logoData ): self {
		$this->logodata = $logoData;

		return $this;
	}

	public function getShips(): Collection {
		return $this->ships;
	}

	public function setShips( array $ships ): self {
		$this->ships = new ArrayCollection( $ships );

		return $this;
	}
}