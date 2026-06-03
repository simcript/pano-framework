<?php

namespace Pano\Kernel;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class BaseBag implements ArrayAccess, IteratorAggregate, Countable
{

    public function __construct(protected array $items = [])
    {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, mixed $value): static
    {
        $this->items[$key] = $value;
        return $this;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function remove(string $key): static
    {
        unset($this->items[$key]);
        return $this;
    }

    /* =========================
     |  Interfaces
     ========================= */

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

}