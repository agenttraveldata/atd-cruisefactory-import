<?php

namespace ATD\CruiseFactory\Entity;

use DateTimeInterface;
use Exception;

class Log {
	private int $id;
	private string $type;
	private string $message;
	private DateTimeInterface $dateTime;

	public function getId(): int {
		return $this->id;
	}

	public function getType(): string {
		return $this->type;
	}

	/**
	 * @throws Exception
	 */
	public function setType( string $type ): self {
		$types = [
			'add'    => true,
			'modify' => true,
			'remove' => true,
			'info'   => true,
			'error'  => true,
		];

		if ( ! isset( $types[ $type ] ) ) {
			throw new Exception( 'Invalid log type: ' . $type );
		}

		$this->type = $type;

		return $this;
	}

	public function getMessage(): string {
		return $this->message;
	}

	public function setMessage( string $message ): self {
		$this->message = $message;

		return $this;
	}

	public function getDateTime(): DateTimeInterface {
		return $this->dateTime;
	}

	public function setDateTime( DateTimeInterface $dateTime ): self {
		$this->dateTime = $dateTime;

		return $this;
	}
}