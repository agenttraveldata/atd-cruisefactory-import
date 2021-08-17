<?php


namespace ATD\CruiseFactory\Entity;


class LatLong {
	public int $id;
	private ?Port $port;
	private float $lat;
	private float $long;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getPort(): Port {
		return $this->port;
	}

	public function setPort( Port $port ): self {
		$this->port = $port;

		return $this;
	}

	public function getLat(): float {
		return $this->lat;
	}

	public function setLat( float $lat ): self {
		$this->lat = $lat;

		return $this;
	}

	public function getLong(): float {
		return $this->long;
	}

	public function setLong( float $long ): self {
		$this->long = $long;

		return $this;
	}
}