<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\DateTimeWrapper;
use PHPUnit\Framework\TestCase;

const DATE_FORMAT_WITH_TIMEZONE = 'Y-m-d H:i:s T';

class DateTimeWrapperTest extends TestCase
{
    public function getNextMonthWithSameDayFromDateProvider(): array
    {
        return [
            ['2024-01-15 14:23:40 UTC', 1, '2024-02-15 00:00:00 UTC'],
            ['2024-01-31 14:23:40 UTC', 1, '2024-02-29 00:00:00 UTC'],
            ['2023-01-31 14:23:40 UTC', 1, '2023-02-28 00:00:00 UTC'],

            ['2024-06-29 14:23:40 UTC', 6, '2024-12-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 6, '2024-12-29 00:00:00 UTC'],

            ['2024-06-29 14:23:40 UTC', 1, '2024-07-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 2, '2024-08-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 3, '2024-09-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 4, '2024-10-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 5, '2024-11-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 6, '2024-12-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 7, '2025-01-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 8, '2025-02-28 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 9, '2025-03-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 10, '2025-04-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 11, '2025-05-29 00:00:00 UTC'],
            ['2024-06-29 14:23:40 UTC', 12, '2025-06-29 00:00:00 UTC']
        ];
    }

    public function test_copy_returns_a_new_instance_of_date_time_wrapper()
    {
        $expected = new DateTimeWrapper('2024-01-15 14:23:40 UTC');
        $copy = $expected->copy();

        $this->assertInstanceOf(DateTimeWrapper::class, $copy);
        $this->assertEquals($expected, $copy);
    }

    public function test_copy_date_returns_a_new_instance_of_date_time()
    {
        $expected = new DateTimeWrapper('2024-01-15 14:23:40 UTC');
        $copy = $expected->copyDate();

        $this->assertInstanceOf(\DateTime::class, $copy);
        $this->assertEquals(
            $expected->format(DATE_FORMAT_WITH_TIMEZONE),
            $copy->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    public function test_copy_date_time_immutable_returns_a_new_instance_of_date_time_immutable()
    {
        $expected = new DateTimeWrapper('2024-01-15 14:23:40 UTC');

        $copy = $expected->copyDateImmutable();

        $this->assertInstanceOf(\DateTimeImmutable::class, $copy);
        $this->assertEquals(
            $expected->format(DATE_FORMAT_WITH_TIMEZONE),
            $copy->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    public function test_format_return_a_correct_string()
    {
        $expected = '2024-01-15 14:23:40 UTC';

        $date = new DateTimeWrapper("2024-01-15 14:23:40 UTC");

        $this->assertEquals(
            $expected, 
            $date->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    public function test_set_time_should_set_time_correctly()
    {
        $expected = '2024-01-15 14:23:40 UTC';

        $date = new DateTimeWrapper('2024-01-15 01:02:03 UTC');
        $date->setTime(14, 23, 40);

        $this->assertEquals(
            $expected, 
            $date->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    public function test_format_should_return_a_correct_string()
    {
        $expected = '2024-01-15 14:23:40 UTC';

        $date = new DateTimeWrapper('2024-01-15 14:23:40 UTC');

        $this->assertEquals(
            $expected, 
            $date->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    public function test_difference_to_should_return_a_correct_date_interval()
    {
        $to = new \DateTime('2024-01-16 01:01:01 UTC');
        $expected = (new \DateTime('2024-01-15 00:00:00 UTC'))->diff($to);

        $from = new DateTimeWrapper('2024-01-15 00:00:00 UTC');

        $this->assertEquals(
            $expected, 
            $from->differenceTo($to)
        );
    }

    public function test_difference_to_with_time_normalized_should_return_a_correct_date_interval()
    {
        $expected = (new \DateTime('2024-01-15 00:00:00 UTC'))->diff(
            new \DateTime('2024-10-16 00:00:00 UTC')
        );

        $from = new DateTimeWrapper('2024-01-15 20:15:30 UTC');
        $to = new \DateTime('2024-10-16 12:58:46 UTC');

        $this->assertEquals(
            $expected, 
            $from->differenceToWithTimeNormalized($to)
        );
    }

    /**
     * @dataProvider getNextMonthWithSameDayFromDateProvider
     */
    public function test_get_next_month_with_same_day_from_date_should_return_a_correct_date(
        string $from,
        int $addMonths,
        string $expected
    )
    {
        $from = new DateTimeWrapper($from);

        $act = $from->getNextMonthWithSameDayFromDate(new \DateTimeImmutable($from->format(DATE_FORMAT_WITH_TIMEZONE)), $addMonths);

        $this->assertEquals(
            $expected,
            $act->copyDate()->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    /**
     * @dataProvider getNextMonthWithSameDayFromDateProvider
     */
    public function test_get_next_month_with_same_day_should_return_a_correct_date(
        string $from,
        int $addMonths,
        string $expected
    ) {
        $from = new DateTimeWrapper($from);

        $act = $from->getNextMonthWithSameDay( $addMonths);

        $this->assertEquals(
            $expected,
            $act->copyDate()->format(DATE_FORMAT_WITH_TIMEZONE)
        );
    }

    // getNormalizedMonthDiff
    public function test_get_normalized_month_diff_should_return_a_correct_date()
    {
        $from = new DateTimeWrapper('2024-01-15 14:23:40 UTC');
        $to = (new DateTimeWrapper('2026-10-16 12:58:46 UTC'))->copyDateImmutable();
        $expected = 33;

        $this->assertEquals(
            $expected, 
            $from->getNormalizedMonthDiff($to)
        );

    }

    public function test_to_utc_time_string_should_return_a_correct_string()
    {
        $expected = '2024-01-15T14:23:40.000000Z';

        $date = new DateTimeWrapper('2024-01-15T14:23:40.000000Z');

        $this->assertEquals(
            $expected, 
            $date->toUTCTimeString()
        );
    }

    public function test_is_before_should_return_a_correct_boolean()
    {
        $from = new DateTimeWrapper('2024-01-15 14:23:40 UTC');
        $to = new DateTimeWrapper('2024-01-16 14:23:40 UTC');

        $this->assertTrue($from->isBefore($to));
        $this->assertFalse($to->isBefore($from));
    }

    public function test_is_after_should_return_a_correct_boolean()
    {
        $from = new DateTimeWrapper('2025-02-15 14:23:40 UTC');
        $to = new DateTimeWrapper('2024-01-16 14:23:40 UTC');

        $this->assertTrue($from->isAfter($to));
        $this->assertFalse($to->isAfter($from));
    }
}
