<?php


namespace ATD\CruiseFactory\Entity;


class Itinerary {
	private int $id;
	private int $day;
	private ?Port $port;
	private string $arrive;
	private string $depart;
	private int $portorder;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getDay(): int {
		return $this->day;
	}

	public function setDay( int $day ): self {
		$this->day = $day;

		return $this;
	}

	public function getPort(): ?Port {
		return $this->port ?? null;
	}

	public function setPort( Port $port ): self {
		$this->port = $port;

		return $this;
	}

	public function getArrive(): string {
		return $this->arrive;
	}

	public function setArrive( string $arrive ): self {
		$this->arrive = $arrive;

		return $this;
	}

	public function getDepart(): string {
		return $this->depart;
	}

	public function setDepart( string $depart ): self {
		$this->depart = $depart;

		return $this;
	}

	public function getPortOrder(): int {
		return $this->portorder;
	}

	public function setPortOrder( int $portOrder ): self {
		$this->portorder = $portOrder;

		return $this;
	}
}