<?php


namespace ATD\CruiseFactory\Entity;


class Port {
	public int $id;
	private string $name;
	private string $description;
	private ?LatLong $latLong;

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

	public function getLatLong(): ?LatLong {
		return $this->latLong ?? null;
	}

	public function setLatLong( LatLong $latLong ): self {
		$this->latLong = $latLong;

		return $this;
	}
}