<?php


namespace ATD\CruiseFactory\Entity;


class Deck {
	public int $id;
	private ?Ship $ship;
	private string $name;
	private string $level;
	private string $image;
	private string $colorcode;
	private string $providerImageUrl = 'https://ik.imagekit.io/atd/ships/deckplans/';

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

	public function getLevel(): string {
		return $this->level;
	}

	public function setLevel( string $level ): self {
		$this->level = $level;

		return $this;
	}

	public function getImage(): string {
		return $this->providerImageUrl . $this->image;
	}

	public function setImage( string $image ): self {
		$this->image = $image;

		return $this;
	}

	public function getColorCode(): string {
		return $this->colorcode;
	}

	public function setColorCode( string $colorCode ): self {
		$this->colorcode = $colorCode;

		return $this;
	}
}