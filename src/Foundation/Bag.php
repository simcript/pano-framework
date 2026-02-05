<?php

namespace Pano\Foundation;

use Pano\Core\BaseBag;

final class Bag extends BaseBag
{

    public function merge(BaseBag $items): static
    {
        return new static(
            array_merge($this->items, $items->all())
        );
    }

    public function replace(BaseBag $items): static
    {
        return new static(
            array_replace($this->items, $items->all())
        );
    }

    /* =========================
     |  Helpers
     ========================= */

    public function only(array $keys): static
    {
        return new static(
            array_intersect_key($this->items, array_flip($keys))
        );
    }

    public function except(array $keys): static
    {
        return new static(
            array_diff_key($this->items, array_flip($keys))
        );
    }

    public function map(callable $callback): static
    {
        return new static(
            array_map($callback, $this->items)
        );
    }

    public function filter(callable $callback): static
    {
        return new static(
            array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH)
        );
    }

    public function find(mixed $value, bool $strict = true): string|null {
        $results = [];
        $this->search(
            $this->items,
            $value,
            $strict,
            $results
        );
        return $results[0] ?? null;
    }

    public function findAll(mixed $value, bool $strict = true): array {
        $results = [];
        $this->search(
            $this->items,
            $value,
            $strict,
            $results
        );
        return $results;
    }

    protected function search(
        array $items,
        mixed $value,
        bool $strict,
        array &$results,
        string $path = ''
    ): void {
        foreach ($items as $key => $item) {
            $currentPath = $path === ''
                ? (string) $key
                : $path . '.' . $key;

            if (is_array($item)) {
                $this->search(
                    $item,
                    $value,
                    $strict,
                    $results,
                    $currentPath
                );
            } else {
                if ($strict ? $item === $value : $item == $value) {
                    $results[] = $currentPath;
                }
            }
        }
    }
}
