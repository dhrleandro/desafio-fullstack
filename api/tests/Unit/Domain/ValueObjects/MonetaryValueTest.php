<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\MonetaryValue;
use PHPUnit\Framework\TestCase;

class MonetaryValueTest extends TestCase
{
    public function monetaryValuesRoundProvider(): array
    {
        return [
            [0.2249, 0.22],
            [3.33333, 3.33],
            [28.1258, 28.13],
            [999.789333, 999.79]
        ];
    }

    public function test_value_returns_correct_value()
    {
        $expected = 58.97;
        $monetary = MonetaryValue::create($expected);
        $this->assertEquals($expected, $monetary->value());
    }

    /**
     * @dataProvider monetaryValuesRoundProvider
     */
    public function test_round_returns_correct_value(
        float $input,
        float $expected
    )
    {
        $monetary = MonetaryValue::create($input);
        $this->assertEquals($expected, $monetary->value());
    }

    public function test_sum_returns_correct_value()
    {
        $expected = 49.20;
        $monetary = MonetaryValue::create(28.10);

        $monetary->sum(21.10);

        $this->assertEquals($expected, $monetary->value());
    }

    public function test_sub_returns_correct_value()
    {
        $expected = 980.20;
        $monetary = MonetaryValue::create(1000);

        $monetary->sub(19.80);

        $this->assertEquals($expected, $monetary->value());
    }

    public function test_to_string_returns_correct_value()
    {
        $expected = '172.58';
        $monetary = MonetaryValue::create(172.579997);
        $this->assertEquals($expected, $monetary->toString());
    }
}
