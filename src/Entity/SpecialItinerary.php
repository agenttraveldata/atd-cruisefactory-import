<?php


namespace ATD\CruiseFactory\Entity;


class SpecialItinerary {
	private int $id;
	private int $day;
	private string $activity;
	private string $starttime;
	private string $endtime;
	private string $type;
	private int $order;

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

	public function getActivity(): string {
		return $this->activity;
	}

	public function setActivity( string $activity ): self {
		$this->activity = $activity;

		return $this;
	}

	public function getStartTime(): string {
		return $this->starttime;
	}

	public function setStartTime( string $startTime ): self {
		$this->starttime = $startTime;

		return $this;
	}

	public function getEndTime(): string {
		return $this->endtime;
	}

	public function setEndTime( string $endTime ): self {
		$this->endtime = $endTime;

		return $this;
	}

	public function getType(): string {
		return $this->type;
	}

	public function setType( string $type ): self {
		$this->type = $type;

		return $this;
	}

	public function getOrder(): int {
		return $this->order;
	}

	public function setOrder( int $order ): self {
		$this->order = $order;

		return $this;
	}
}