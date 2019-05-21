<?php

namespace LuKun\Structures;

class Sequence
{
    /** @var mixed */
    private $lastValue;
    /** @var callable $nextValue - (mixed $lastValue): mixed */
    private $nextValue;

    /**
     * @param mixed $startValue
     * @param callable $nextValue - (mixed $lastValue): mixed
     * */
    public function __construct($startValue, callable $nextValue)
    {
        $this->lastValue = $startValue;
        $this->nextValue = $nextValue;
    }

    /** @return mixed */
    public function getLastValue()
    {
        return $this->lastValue;
    }

    /** @return mixed */
    public function getNextValue()
    {
        return $this->nextValue($this->lastValue);
    }
}
