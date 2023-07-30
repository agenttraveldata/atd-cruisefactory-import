<?php


namespace ATD\CruiseFactory\Services\Data\Collections;


use ArrayIterator;
use Closure;

class ArrayCollection implements Collection {
	private array $elements;

	public function __construct( array $elements = [] ) {
		$this->elements = $elements;
	}

	public function add( $element ): void {
		$this->elements[] = $element;
	}

	public function clear(): void {
		$this->elements = [];
	}

	public function contains( $element ): bool {
		return in_array( $element, $this->elements, true );
	}

	public function isEmpty(): bool {
		return empty( $this->elements );
	}

	public function remove( $key ): mixed {
		if ( $this->containsKey( $key ) ) {

			$removed = $this->elements[ $key ];
			unset( $this->elements[ $key ] );

			return $removed;
		}

		return null;
	}

	public function removeElement( $element ): bool {
		if ( $key = array_search( $element, $this->elements, true ) ) {
			unset( $this->elements[ $key ] );

			return true;
		}

		return false;
	}

	public function containsKey( $key ): bool {
		return isset( $this->elements[ $key ] ) || array_key_exists( $key, $this->elements );
	}

	public function get( $key ): mixed {
		return $this->elements[ $key ] ?? null;
	}

	public function getKeys(): array {
		return array_keys( $this->elements );
	}

	public function getValues(): array {
		return array_values( $this->elements );
	}

	public function set( $key, $value ): void {
		$this->elements[ $key ] = $value;
	}

	public function toArray(): array {
		return $this->elements;
	}

	public function first(): mixed {
		return reset( $this->elements );
	}

	public function last(): mixed {
		return end( $this->elements );
	}

	public function key(): int {
		return key( $this->elements );
	}

	public function current(): mixed {
		return current( $this->elements );
	}

	public function next(): mixed {
		return next( $this->elements );
	}

	public function exist( Closure $closure ): bool {
		foreach ( $this->elements as $key => $element ) {
			if ( $closure( $key, $element ) ) {
				return true;
			}
		}

		return false;
	}

	public function filter( Closure $closure ): self {
		return new static( array_filter( $this->elements, $closure ) );
	}

	public function indexOf( $element ): false|int {
		return array_search( $element, $this->elements, true );
	}

	public function slice( int $offset, ?int $length = null ): array {
		return array_slice( $this->elements, $offset, $length, true );
	}

	public function count(): int {
		return count( $this->elements );
	}

	public function map( Closure $closure ): self {
		return new static( array_map( $closure, $this->elements ) );
	}

	public function getIterator(): ArrayIterator {
		return new ArrayIterator( $this->elements );
	}

	public function offsetExists( $offset ): bool {
		return $this->containsKey( $offset );
	}

	public function offsetGet( $offset ): mixed {
		return $this->get( $offset );
	}

	public function offsetSet( $offset, $value ): void {
		if ( ! isset( $offset ) ) {
			$this->add( $value );

			return;
		}

		$this->set( $offset, $value );
	}

	public function offsetUnset( $offset ): void {
		$this->remove( $offset );
	}

	public function uasort( callable $callable ): self {
		uasort( $this->elements, $callable );

		return $this;
	}

	public function usort( callable $callable ): self {
		usort( $this->elements, $callable );

		return $this;
	}
}