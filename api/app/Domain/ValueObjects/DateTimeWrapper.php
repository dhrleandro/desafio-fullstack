<?php

namespace App\Domain\ValueObjects;

const UTC_TIMEZONE = 'UTC';

const DATE_FORMAT_WITH_TIMEZONE = 'Y-m-d H:i:s T';
const DATE_FORMAT_NORMALIZED = 'Y-m-01 00:00:00 T';

class DateTimeWrapper
{
    private \DateTime $date;

    public function __construct(
        string|\DateTime|DateTimeWrapper $time = 'now',
        \DateTimeZone|null $timezone = new \DateTimeZone(UTC_TIMEZONE))
    {
        if ($time instanceof \DateTime) {
            if ($time->getTimezone()->getName() != UTC_TIMEZONE) {
                throw new \InvalidArgumentException('The time must be in UTC');
            }
            $this->date = $time;
            $this->date->setTimezone(new \DateTimeZone(UTC_TIMEZONE));
            return;
        }

        if ($time instanceof DateTimeWrapper) {
            if ($time->copyDate()->getTimezone()->getName() != UTC_TIMEZONE) {
                throw new \InvalidArgumentException('The time must be in UTC');
            }
            $this->date = $time->copyDate();
            $this->date->setTimezone(new \DateTimeZone(UTC_TIMEZONE));
            return;
        }

        $this->date = new \DateTime($time, $timezone);
        $this->date->setTimezone(new \DateTimeZone(UTC_TIMEZONE));
    }

    public function copy(): DateTimeWrapper
    {
        $copy = new DateTimeWrapper(clone $this->date);
        return $copy;
    }

    public function copyDate(): \DateTime
    {
        $copy = clone $this->date;
        return $copy;
    }

    public function format(string $format): string
    {
        return $this->date->format($format);
    }

    function setTime(int $hour = 0, int $minute = 0, int $sec = 0, int $micro = 0): DateTimeWrapper {
        $this->date->setTime($hour, $minute, $sec, $micro);
        return $this;
    }

    public function copyDateImmutable(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->date->format(DATE_FORMAT_WITH_TIMEZONE));
    }

    public function differenceTo(\DateTime $toDate): \DateInterval
    {
        $diff = $this->date->diff($toDate);
        return $diff;
    }

    public function differenceToWithTimeNormalized(\DateTime $toDate): \DateInterval
    {
        $from = new \DateTime($this->date->format(DATE_FORMAT_WITH_TIMEZONE));
        $from->setTime(0, 0, 0, 0);
        $to = new \DateTime($toDate->format(DATE_FORMAT_WITH_TIMEZONE));
        $to->setTime(0, 0, 0, 0);
        return $from->diff($to);
    }

    /**
     * Returns the date from the following month with the same day. 
     * If the day does not exist, returns the last day of the following month.
     * 
     * **Examples**:  
     * 2024-01-15 returns 2024-02-15  
     * 2024-01-31 returns 2024-02-29
     * 
     * @return DateTimeWrapper
     */
    public function getNextMonthWithSameDayFromDate(\DateTimeImmutable $from, int $addMonths = 1): DateTimeWrapper {
        if ($from->getTimezone()->getName() != UTC_TIMEZONE) {
            throw new \InvalidArgumentException('The date must be in UTC');
        }

        $day = $from->format('d');

        $fisrtDayNextMonth = new \DateTime($from->format(DATE_FORMAT_NORMALIZED));
        $fisrtDayNextMonth->modify("+$addMonths month");

        $nextMonth = new \DateTime($fisrtDayNextMonth->format("Y-m-$day 00:00:00 T"));
        $fisrtDayNextMonth->setTimezone(new \DateTimeZone(UTC_TIMEZONE));
    
        if ($nextMonth->format('m') != $fisrtDayNextMonth->format('m')) {
            $nextMonth = $fisrtDayNextMonth->modify('last day of this month');
        }
    
        return new DateTimeWrapper($nextMonth->format(DATE_FORMAT_WITH_TIMEZONE));
    }

    public function getNextMonthWithSameDay(int $addMonths = 1): DateTimeWrapper {
        return $this->getNextMonthWithSameDayFromDate($this->copyDateImmutable(), $addMonths);
    }

    public function getNormalizedMonthDiff(\DateTimeImmutable $to): int {
        if ($to->getTimezone()->getName() != UTC_TIMEZONE) {
            throw new \InvalidArgumentException('The date must be in UTC');
        }

        $normalizedTo = new \DateTimeImmutable($to->format(DATE_FORMAT_NORMALIZED));
        if ($normalizedTo->getTimezone()->getName() != UTC_TIMEZONE) {
            throw new \InvalidArgumentException('The date must be in UTC');
        }

        $normalizedFrom = new \DateTimeImmutable($this->date->format(DATE_FORMAT_NORMALIZED));
        if ($normalizedFrom->getTimezone()->getName() != UTC_TIMEZONE) {
            throw new \InvalidArgumentException('The date must be in UTC');
        }

        $diff = $normalizedTo->diff($normalizedFrom);
        $months = $diff->m;
        $years = $diff->y;

        return $years * 12 + $months;
    }

    public function toUTCTimeString(): string
    {
        return $this->date->format(DATE_FORMAT_WITH_TIMEZONE);
    }
}
