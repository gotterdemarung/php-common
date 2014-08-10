<?php

namespace Gsr\DateTime;

use Gsr\Exception\Argument\NotIntegerException;

/**
 * Represents local (bound to provided timezone) time
 *
 * @package Gsr\DateTime
 */
class LocalTime implements InstantInterface
{
    private $year;
    private $month;
    private $day;
    private $hour;
    private $minute;
    private $second;
    private $microseconds;
    private $timezone;

    /**
     * Converts provided instant into local time with provided timezone
     *
     * @param \DateTimeZone    $timeZone
     * @param InstantInterface $instant
     * @return LocalTime
     */
    public static function convert(\DateTimeZone $timeZone, InstantInterface $instant)
    {
        // Timestamp is always in UTC
        $ts    = $instant->toFloat();
        $unix  = intval($ts);
        $micro = intval(($ts - $unix) * 1000000);
        $date  = new \DateTime('@' . $unix, $timeZone);
        $parts = array_map('intval', explode('-', $date->format('Y-m-d-H-i-s')));

        return new LocalTime(
            $timeZone,
            $parts[0],
            $parts[1],
            $parts[2],
            $parts[3],
            $parts[4],
            $parts[5],
            $micro
        );
    }

    /**
     * Constructor
     *
     * Does not validate incoming arguments, so it is possible to send 25 as $hour and 13 as $month
     *
     * @param \DateTimeZone $timezone
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $microseconds
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \DateTimeZone $timezone,
        $year,
        $month,
        $day,
        $hour = 0,
        $minute = 0,
        $second = 0,
        $microseconds = 0
    ) {
        if (!is_int($year)) {
            throw new NotIntegerException('year');
        }
        if (!is_int($month)) {
            throw new NotIntegerException('month');
        }
        if (!is_int($day)) {
            throw new NotIntegerException('day');
        }
        if (!is_int($hour)) {
            throw new NotIntegerException('hour');
        }
        if (!is_int($minute)) {
            throw new NotIntegerException('minute');
        }
        if (!is_int($second)) {
            throw new NotIntegerException('second');
        }
        if (!is_int($microseconds)) {
            throw new NotIntegerException('microseconds');
        }

        $this->day = $day;
        $this->hour = $hour;
        $this->microseconds = $microseconds;
        $this->minute = $minute;
        $this->month = $month;
        $this->second = $second;
        $this->timezone = $timezone;
        $this->year = $year;
    }

    /**
     * Returns day
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Returns hour
     *
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Returns microseconds
     *
     * @return int
     */
    public function getMicroseconds()
    {
        return $this->microseconds;
    }

    /**
     * Returns minute
     *
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Returns month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Returns second
     *
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Returns timezone
     *
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Returns year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Returns float representation
     *
     * @return float
     */
    public function toFloat()
    {
        return $this->toInt() + ($this->getMicroseconds() / 1000000);
    }

    /**
     * Returns integer representation (unix timestamp)
     *
     * @return int
     */
    public function toInt()
    {
        return $this->toDateTime()->getTimestamp();
    }

    /**
     * Return PHP DateTime object
     *
     * @return \DateTime
     */
    public function toDateTime()
    {
        $dt = new \DateTime('now', $this->getTimezone());
        $dt->setDate($this->getYear(), $this->getMonth(), $this->getDay());
        $dt->setTime($this->getHour(), $this->getMinute(), $this->getSecond());
        return $dt;
    }

    /**
     * Returns true if timestamp value of instant equals to provided one
     *
     * @param mixed $value
     * @return boolean
     */
    public function equals($value)
    {
        switch (true) {
            case $value === null:
                return false;
            case is_int($value):
            case is_float($value):
                return $value == $this->toFloat();
            case $value instanceof InstantInterface:
                return $value->toFloat() === $this->toFloat();
            default:
                return false;
        }
    }
}
