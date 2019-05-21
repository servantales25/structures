<?php

namespace LuKun\Structures;

use InvalidArgumentException;

class Flag
{
    /** @var int */
    private const MIN = 0;
    /** @var int */
    private const MAX = 9223372036854775807;

    /** @var int */
    private $value;

    public function __construct()
    {
        $this->value = 0;
    }

    public function isEmpty(): bool
    {
        return $this->value === 0;
    }

    public function isActivated(int $switch): bool
    {
        return $this->_isActivated($switch);
    }

    public function activate(int $switch): bool
    {
        if ($switch > 62 || $switch < 0) {
            throw new InvalidArgumentException('Switch argument for flag activation must be in range from 0 to 62.');
        }

        if (!$this->_isActivated($switch)) {
            $this->value |= 2 ** $switch;

            return true;
        }

        return false;
    }

    public function deactivate(int $switch): bool
    {
        if ($switch > 62 || $switch < 0) {
            throw new InvalidArgumentException('Switch argument for flag deactivation must be in range from 0 to 62.');
        }

        if (!$this->_isActivated($switch)) {
            $this->value &= ~(2 ** $switch);

            return true;
        }

        return false;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function toString(): string
    {
        return decbin($this->value);
    }

    public static function fromInt(int $value): Flag
    {
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidArgumentException(sprintf('Integer value for Flag reconstruction has to be in range from %d to %d.', self::MIN, self::MAX));
        }

        $flag = new Flag();
        $flag->value = $value;

        return $flag;
    }

    public static function fromString(string $value): Flag
    {
        if (preg_match('/^[10]*$/', $value) !== 1) {
            throw new InvalidArgumentException('Binary string value for Flag reconstruction should contain only ones or zeroes.');
        }
        if (strlen($value) > 63) {
            throw new InvalidArgumentException('Binary string value for Flag reconstruction cannot be longer than 63 characters.');
        }

        $intValue = bindec($value);
        $flag = new Flag();
        $flag->value = $intValue;

        return $flag;
    }

    private function _isActivated(int $switch): bool
    {
        $flag = 2 ** $switch;
        $result = $this->value & $flag;

        return $result !== 0;
    }
}
