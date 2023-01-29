<?php


namespace ATD\CruiseFactory\Entity;


class Cabin {
	public int $id;
	private ?Ship $ship;
	private string $name;
	private string $description;
	private string $image;
	private string $photo;
	private string $cabin_order;
	private string $cabin_category;
	private string $providerImageUrl = 'https://ik.imagekit.io/atd/ships/cabins/';
	private string $providerPhotoUrl = 'https://ik.imagekit.io/atd/ships/cabinphotos/';

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

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription( string $description ): self {
		$this->description = $description;

		return $this;
	}

	public function hasImage(): bool {
		return ! empty( $this->image );
	}

	public function getImage(): string {
		return $this->providerImageUrl . $this->image;
	}

	public function setImage( string $image ): self {
		$this->image = $image;

		return $this;
	}

	public function getPhoto(): string {
		return $this->providerPhotoUrl . $this->photo;
	}

	public function setPhoto( string $photo ): self {
		$this->photo = $photo;

		return $this;
	}

	public function getOrder(): string {
		return $this->cabin_order;
	}

	public function setOrder( string $order ): self {
		$this->cabin_order = $order;

		return $this;
	}

	public function getCategory(): string {
		return $this->cabin_category;
	}

	public function setCategory( string $cabinCategory ): self {
		$this->cabin_category = $cabinCategory;

		return $this;
	}

	public function getProviderImageUrl(): string {
		return $this->providerImageUrl;
	}

	public function getProviderPhotoUrl(): string {
		return $this->providerPhotoUrl;
	}
}