<?php


namespace ATD\CruiseFactory\Services\Data\Collections;

use ArrayAccess;
use Closure;
use Countable;
use IteratorAggregate;

interface Collection extends Countable, IteratorAggregate, ArrayAccess {
	public function add( $element ): void;
	public function clear(): void;
	public function contains( $element ): bool;
	public function isEmpty(): bool;
	public function map( Closure $closure ): self;
	public function remove( $key );
	public function removeElement( $element ): bool;
	public function containsKey( $key ): bool;
	public function get( $key );
	public function getKeys(): array;
	public function getValues(): array;
	public function set( $key, $value ): void;
	public function toArray(): array;
	public function first();
	public function last();
	public function key();
	public function current();
	public function next();
	public function exist( Closure $closure ): bool;
	public function filter( Closure $closure ): self;
	public function indexOf( $element );
	public function slice( int $offset, ?int $length = null ): array;
	public function count(): int;
	public function uasort( callable $callable ): self;
	public function usort( callable $callable ): self;
}