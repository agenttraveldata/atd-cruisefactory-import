<?php


namespace ATD\CruiseFactory\Entity;


class Factory {
	public int $id;
	private string $name;
	private string $booking_email;
	private ?Currency $currency;

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

	public function getBookingEmail(): string {
		return $this->booking_email;
	}

	public function setBookingEmail( string $bookingEmail ): self {
		$this->booking_email = $bookingEmail;

		return $this;
	}

	public function getCurrency(): ?Currency {
		return $this->currency ?? null;
	}

	public function setCurrency( Currency $currency ): self {
		$this->currency = $currency;

		return $this;
	}
}