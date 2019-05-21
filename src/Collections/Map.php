<?php

namespace LuKun\Structures\Collections;

class Map
{
    private $array;
    /** @var int */
    private $length;

    public function __construct()
    {
        $this->array = [];
        $this->length = 0;
    }

    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function contains($value): bool
    {
        return in_array($value, $this->array, true);
    }

    /** @return mixed|null */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->array)) {
            return $this->array[$key];
        }

        return null;
    }

    public function getKeys(): array
    {
        return array_keys($this->array);
    }

    public function getValues(): array
    {
        return array_values($this->array);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->array);
    }

    public function filter(callable $predicate): Map
    {
        $map = new Map();
        foreach ($this->array as $key => $value) {
            if ($predicate($value)) {
                $map->set($key, $value);
            }
        }

        return $map;
    }

    public function map(callable $selector): Map
    {
        $map = new Map();
        foreach ($this->array as $key => $value) {
            $map->set($key, $selector($value));
        }

        return $map;
    }

    public function set(string $key, $value): void
    {
        if (!isset($this->array[$key])) {
            $this->length++;
        }

        $this->array[$key] = $value;
    }

    public function drop(string $key): bool
    {
        if (array_key_exists($key, $this->array)) {
            unset($this->array[$key]);
            $this->length--;

            return true;
        }

        return false;
    }

    public function dropAll(string ...$keys): int
    {
        $count = 0;
        foreach ($keys as $key) {
            if ($this->drop($key)) {
                $count++;
            }
        }

        return $count;
    }

    public function remove($value): int
    {
        $count = 0;
        foreach ($this->array as $key => $_value) {
            if ($_value === $value) {
                $this->drop($key);
                $count++;
            }
        }

        return $count;
    }

    public function removeAll(callable $predicate): int
    {
        $count = 0;
        foreach ($this->array as $key => $value) {
            if ($predicate($key, $value)) {
                $this->drop($key);
                $count++;
            }
        }

        return $count;
    }

    public function clear(): void
    {
        $this->array = [];
        $this->length = 0;
    }

    public function clone(): Map
    {
        $map = new Map();
        $map->array = $this->array;
        $map->length = $this->length;

        return $map;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public static function fromArray(array $array): Map
    {
        $map = new Map();
        foreach ($array as $key => $value) {
            $map->set($key, $value);
        }

        return $map;
    }
}
