<?php

namespace App\Domain\ValueObjects;

class MonetaryValue
{
    private float $value;

    private function __construct(float $value)
    {
        $this->value = $value;
        $this->round();
    }

    public static function create(float $value): MonetaryValue
    {
        return new self($value);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function toString(): string
    {
        return sprintf("%.2f", $this->value);
    }

    public function round()
    {
        $this->value = round($this->value, 2, PHP_ROUND_HALF_UP);
    }

    public function sum(float $amount): MonetaryValue
    {
        $this->value += $amount;
        return $this;
    }

    public function sub(float $amount): MonetaryValue
    {
        $this->value -= $amount;
        return $this;
    }
}
