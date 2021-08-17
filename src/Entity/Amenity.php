<?php


namespace ATD\CruiseFactory\Entity;


class Amenity {
	public int $id;
	private ?Ship $ship;
	private string $name;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getShip(): ?Ship {
		return $this->ship ?? null;
	}

	public function setShip( Ship $ship ): self {
		$this->ship = $ship;

		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName( string $name ): self {
		$this->name = $name;

		return $this;
	}
}