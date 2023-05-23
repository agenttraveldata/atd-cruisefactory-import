<?php


namespace ATD\CruiseFactory\Entity;


class SpecialDeparture {
	public int $id;
	private Departure $departure;
	private ?Special $special;

	public function getId(): int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getSailingDate(): ?Departure {
		return $this->departure ?? null;
	}

	public function setSailingDate( Departure $departure ): self {
		$this->departure = $departure;

		return $this;
	}

	public function getSpecial(): ?Special {
		return $this->special ?? null;
	}

	public function setSpecial( Special $special ): self {
		$this->special = $special;

		return $this;
	}
}