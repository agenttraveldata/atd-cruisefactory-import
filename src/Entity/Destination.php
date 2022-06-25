<?php


namespace ATD\CruiseFactory\Entity;


class Destination {
	public int $id;
	private string $name;
	private string $featured_text;
	private string $description;
	private string $map_large;

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
		return $this->featured_text;
	}

	public function setBriefDescription( string $briefDescription ): self {
		$this->featured_text = $briefDescription;

		return $this;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription( string $description ): self {
		$this->description = $description;

		return $this;
	}

	public function getMapLarge(): string {
		return $this->map_large;
	}

	public function setMapLarge( string $mapLarge ): self {
		$this->map_large = $mapLarge;

		return $this;
	}
}