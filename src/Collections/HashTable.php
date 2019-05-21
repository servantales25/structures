<?php

namespace LuKun\Structures\Collections;

class HashTable
{
    /** @var Map */
    private $table;

    public function __construct()
    {
        $this->table = new Map();
    }

    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    public function contains($value): bool
    {
        foreach ($this->table->getKeys() as $hash) {
            $vector = $this->table->get($hash);
            if ($vector !== null && $vector->contains($value)) {
                return true;
            }
        }

        return false;
    }

    public function containsOf(string $hash, $value): bool
    {
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            return $vector->contains($value);
        }

        return false;
    }

    public function containsAnyOf(string $hash): bool
    {
        return $this->table->has($hash);
    }

    public function getLength(): int
    {
        $length = 0;
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            $length += $this->getLengthOf($hash);
        }

        return $length;
    }

    public function getLengthOf(string $hash): int
    {
        $vector = $this->table->get($hash) ?? new Vector();

        return $vector->getLength();
    }

    /** @return mixed|null */
    public function get(string $hash, int $index)
    {
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            return $vector->get($index);
        }

        return null;
    }

    public function filter(callable $predicate): HashTable
    {
        $hashTable = new HashTable();
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            /** @var Vector $vector */
            $vector = $this->table->get($hash);
            $hashTable->table->set($hash, $vector->filter($predicate));
        }

        return $hashTable;
    }

    public function filterOf(string $hash, callable $predicate): HashTable
    {
        $hashTable = new HashTable();
        $hashTable->table = $this->table->clone();
        $vector = $hashTable->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            $hashTable->table->set($hash, $vector->filter($predicate));
        }

        return $hashTable;
    }

    public function map(callable $selector): HashTable
    {
        $hashTable = new HashTable();
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            /** @var Vector $vector */
            $vector = $this->table->get($hash);
            $hashTable->table->set($hash, $vector->map($selector));
        }

        return $hashTable;
    }

    public function mapOf(string $hash, callable $selector): HashTable
    {
        $hashTable = new HashTable();
        $hashTable->table = $this->table->clone();
        $vector = $hashTable->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            $hashTable->table->set($hash, $vector->map($selector));
        }

        return $hashTable;
    }

    public function walk(callable $handleValue): void
    {
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            /** @var Vector $vector */
            $vector = $this->table->get($hash);
            $vector->walk($handleValue);
        }
    }

    public function walkReverse(callable $handleValue): void
    {
        $hashes = $this->table->getKeys();
        $hashes = array_reverse($hashes);
        foreach ($hashes as $hash) {
            /** @var Vector $vector */
            $vector = $this->table->get($hash);
            $vector->walkReverse($handleValue);
        }
    }

    public function walkOf(string $hash, callable $handleValue): void
    {
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            $vector->walk($handleValue);
        }
    }

    public function walkReverseOf(string $hash, callable $handleValue): void
    {
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            /** @var Vector $vector */
            $vector->walkReverse($handleValue);
        }
    }

    public function addTo(string $hash, $value): int
    {
        /** @var Vector|null $vector */
        $vector = $this->table->get($hash);
        if ($vector === null) {
            $vector = new Vector();
            $this->table->set($hash, $vector);
        }

        return $vector->add($value);
    }

    public function addReverseTo(string $hash, $value): void
    {
        /** @var Vector|null $vector */
        $vector = $this->table->get($hash);
        if ($vector === null) {
            $vector = new Vector();
            $this->table->set($hash, $vector);
        }

        $vector->addReverse($value);
    }

    /** @return mixed|null */
    public function cutOf(string $hash)
    {
        $vector = $this->table->get($hash) ?? new Vector();
        $value = $vector->cut();
        if ($vector->isEmpty()) {
            $this->table->drop($hash);
        }

        return $value;
    }

    /** @return mixed|null */
    public function cutReverseOf(string $hash)
    {
        $vector = $this->table->get($hash) ?? new Vector();
        $value = $vector->cutReverse();
        if ($vector->isEmpty()) {
            $this->table->drop($hash);
        }

        return $value;
    }

    public function drop(string $hash): bool
    {
        return $this->table->drop($hash);
    }

    public function remove($value): int
    {
        $count = 0;
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            /** @var Vector $vector */
            $vector = $this->table->get($hash);
            $count += $vector->remove($value);
            if ($vector->isEmpty()) {
                $this->table->drop($hash);
            }
        }

        return $count;
    }

    public function removeOf(string $hash, $value): int
    {
        $count = 0;
        /** @var Vector|null $vector */
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            $count += $vector->remove($value);
            if ($vector->isEmpty()) {
                $this->table->drop($hash);
            }
        }

        return $count;
    }

    public function removeAll(callable $predicate): int
    {
        $count = 0;
        $hashes = $this->table->getKeys();
        foreach ($hashes as $hash) {
            $count += $this->removeAllOf($hash, $predicate);
        }

        return $count;
    }

    public function removeAllOf(string $hash, callable $predicate): int
    {
        $count = 0;
        /** @var Vector|null $vector */
        $vector = $this->table->get($hash);
        if ($vector !== null) {
            $count += $vector->removeAll($predicate);
            if ($vector->isEmpty()) {
                $this->table->drop($hash);
            }
        }

        return $count;
    }

    public function clear(): void
    {
        $this->table->clear();
    }

    public function toArray(): array
    {
        $out = [];
        foreach ($this->table->getKeys() as $hash) {
            $out = array_merge($out, $this->toArrayOf($hash));
        }

        return $out;
    }

    public function toArrayOf(string $hash): array
    {
        /** @var Vector|null $vector */
        $vector = $this->table->get($hash);

        return ($vector !== null) ? $vector->toArray() : [];
    }

    public static function fromArray(array $array): HashTable
    {
        $hashTable = new HashTable();
        foreach ($array as $hash => $values) {
            foreach ($values as $value) {
                $hashTable->add($hash, $value);
            }
        }

        return $hashTable;
    }
}
