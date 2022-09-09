<?php

namespace ATD\CruiseFactory\Entity;

use DateTimeInterface;
use WP_Post;

class DepartureSummary {
	private ?int $id = null;
	private ?string $type = null;
	private ?Special $special = null;
	private ?Cruise $cruise = null;
	private ?SpecialLeadPrice $specialLeadPrice = null;
	private ?SpecialPrice $specialPrice = null;
	private ?CruisePrice $cruisePrice = null;
	private ?Cabin $requestCabin = null;
	private ?DateTimeInterface $sailingDate = null;
	private ?WP_Post $post = null;

	public function getId(): ?int {
		return $this->id;
	}

	public function setId( int $id ): self {
		$this->id = $id;

		return $this;
	}

	public function getType(): ?string {
		return $this->type;
	}

	public function setType( string $type ): self {
		$this->type = $type;

		return $this;
	}

	public function getSpecial(): ?Special {
		return $this->special;
	}

	public function setSpecial( Special $special ): self {
		$this->special = $special;

		return $this;
	}

	public function getCruise(): ?Cruise {
		return $this->cruise;
	}

	public function setCruise( Cruise $cruise ): self {
		$this->cruise = $cruise;

		return $this;
	}

	public function getCruisePrice(): ?CruisePrice {
		return $this->cruisePrice;
	}

	public function setCruisePrice( ?CruisePrice $cruisePrice ): self {
		$this->cruisePrice = $cruisePrice;

		return $this;
	}

	public function getRequestCabin(): ?Cabin {
		return $this->requestCabin;
	}

	public function setRequestCabin( ?Cabin $requestCabin ): self {
		$this->requestCabin = $requestCabin;

		return $this;
	}

	public function getSpecialLeadPrice(): ?SpecialLeadPrice {
		return $this->specialLeadPrice;
	}

	public function setSpecialLeadPrice( SpecialLeadPrice $specialLeadPrice ): self {
		$this->specialLeadPrice = $specialLeadPrice;

		return $this;
	}

	public function getSpecialPrice(): ?SpecialPrice {
		return $this->specialPrice;
	}

	public function setSpecialPrice( ?SpecialPrice $specialPrice ): self {
		$this->specialPrice = $specialPrice;

		return $this;
	}

	public function getSailingDate(): ?DateTimeInterface {
		return $this->sailingDate;
	}

	public function setSailingDate( DateTimeInterface $sailingDate ): self {
		$this->sailingDate = $sailingDate;

		return $this;
	}

	public function getPost(): ?WP_Post {
		return $this->post;
	}

	public function setPost( WP_Post $post ): self {
		$this->post = $post;

		return $this;
	}
}