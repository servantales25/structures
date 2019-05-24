<?php

namespace LuKun\Structures\Collections;

class Vector
{
    /** @var array */
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

    /** @return mixed|null */
    public function get(int $index)
    {
        return $this->array[$index] ?? null;
    }

    public function getFirstIndexOf($value): ?int
    {
        for ($i = 0; $i < $this->length; $i++) {
            if ($this->array[$i] === $value) {
                return $i;
            }
        }

        return null;
    }

    public function getLastIndexOf($value): ?int
    {
        for ($i = $this->length - 1; $i >= 0; $i--) {
            if ($this->array[$i] === $value) {
                return $i;
            }
        }

        return null;
    }

    public function contains($value): bool
    {
        return in_array($value, $this->array, true);
    }

    public function exists(callable $predicate): bool
    {
        foreach ($this->array as $value) {
            if ($predicate($value)) {
                return true;
            }
        }

        return false;
    }

    public function filter(callable $predicate): Vector
    {
        $vector = new Vector();
        for ($i = 0; $i < $this->length; $i++) {
            $value = $this->array[$i];
            if ($predicate($value)) {
                $vector->add($value);
            }
        }

        return $vector;
    }

    public function map(callable $selector): Vector
    {
        $vector = new Vector();
        for ($i = 0; $i < $this->length; $i++) {
            $value = $selector($this->array[$i]);
            $vector->add($value);
        }

        return $vector;
    }

    public function takeFirst(int $amount): Vector
    {
        $vector = new Vector();
        for ($i = 0; $i < $this->length && $i < $amount; $i++) {
            $value = $this->array[$i];
            $vector->add(($value));
        }

        return $vector;
    }

    public function takeLast(int $amount): Vector
    {
        $vector = new Vector();
        for ($i = $this->length - 1; $i >= 0 && $i >= $this->length - $amount; $i--) {
            $value = $this->array[$i];
            $vector->addReverse(($value));
        }

        return $vector;
    }

    public function slice(int $start, int $end): Vector
    {
        $vector = new Vector();
        for ($i = $start; $i < $this->length && $i < $end; $i++) {
            $value = $this->array[$i];
            $vector->add(($value));
        }

        return $vector;
    }

    public function skipFirst(int $amount): Vector
    {
        $vector = new Vector();
        for ($i = $amount - 1; $i < $this->length; $i++) {
            $value = $this->array[$i];
            $vector->add(($value));
        }

        return $vector;
    }

    public function skipLast(int $amount): Vector
    {
        $vector = new Vector();
        for ($i = 0; $i < $this->length - $amount; $i++) {
            $value = $this->array[$i];
            $vector->add(($value));
        }

        return $vector;
    }

    public function orderBy(callable $compareValues): Vector
    {
        $vector = new Vector();
        $vector->array = $this->array;
        $vector->length = $this->length;
        usort($vector->array, $compareValues);

        return $vector;
    }

    public function walk(callable $handleValue): void
    {
        foreach ($this->array as $value) {
            $handleValue($value);
        }
    }

    public function walkReverse(callable $handleValue): void
    {
        for ($i = $this->length - 1; $i >= 0; $i--) {
            $handleValue($this->array[$i]);
        }
    }

    public function set(int $index, $value): bool
    {
        if ($index < 0 || $index >= $this->length) {
            return false;
        }

        $this->array[$index] = $value;

        return true;
    }

    public function add($value): int
    {
        array_push($this->array, $value);
        $this->length++;

        return $this->length - 1;
    }

    public function insert(int $index, $value): bool
    {
        if ($index < 0 || $index > $this->length) {
            return false;
        }

        $pre = array_slice($this->array, 0, $index);
        $post = array_slice($this->array, $index);

        $this->array = array_merge($pre, [$value], $post);

        return true;
    }

    public function addReverse($value): int
    {
        array_unshift($this->array, $value);
        $this->length++;

        return 0;
    }

    /** @return mixed|null */
    public function cut()
    {
        if ($this->length > 0) {
            return array_pop($this->array);
            $this->length--;
        }

        return null;
    }

    /** @return mixed|null */
    public function cutReverse()
    {
        if ($this->length > 0) {
            return array_shift($this->array);
            $this->end--;
        }

        return null;
    }

    public function remove($value): int
    {
        $count = 0;
        $this->array = array_filter($this->array, function ($_value) use ($value, &$count) {
            if ($_value === $value) {
                $count++;
                return false;
            } else {
                return true;
            }
        });

        $this->length -= $count;

        return $count;
    }

    public function removeAt(int $index): bool
    {
        if ($index < 0 || $index >= $this->length) {
            return false;
        }

        if (isset($this->array[$index])) {
            array_splice($this->array, $index, 1);
            $this->length--;

            return true;
        }

        return false;
    }

    public function removeAll(callable $predicate): int
    {
        $count = 0;
        $this->array = array_filter($this->array, function ($value) use ($predicate, &$count) {
            if ($predicate($value)) {
                $count++;
                return false;
            } else {
                return true;
            }
        });

        $this->length -= $count;

        return $count;
    }

    public function clear(): void
    {
        $this->array = [];
        $this->length = 0;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function toString(string $delimiter): string
    {
        return implode($delimiter, $this->array);
    }

    public static function fromArray(array $values): Vector
    {
        $arrayList = new Vector();
        foreach ($values as $value) {
            array_push($arrayList->array, $value);
        }

        return $arrayList;
    }

    public static function fromString(string $delimiter, string $values): Vector
    {
        return self::fromArray(explode($delimiter, $values));
    }
}
