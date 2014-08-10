<?php

namespace Gsr\DateTime;

use Gsr\Exception\Argument\NotStringException;

/**
 * Time provider
 *
 * @package Gsr\DateTime
 */
class Calendar
{
    private static $instance;

    /**
     * Returns instance
     *
     * @return Calendar
     */
    public static function getUTC()
    {
        if (self::$instance === null) {
            self::$instance = new self(new \DateTimeZone('UTC'));
        }

        return self::$instance;
    }

    /**
     * @var \DateTimeZone
     */
    private $timezone;

    /**
     * Constructs new calendar for provided timezone
     *
     * @param \DateTimeZone $timeZone
     */
    public function __construct(\DateTimeZone $timeZone)
    {
        $this->timezone = $timeZone;
    }

    /**
     * Returns current time
     *
     * @return InstantInterface
     */
    public function getCurrent()
    {
        return new Timestamp(microtime(true));
    }

    /**
     * Returns timezone, used in calendar
     *
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Converts provided instant to local time, using calendar's timezone
     *
     * @param InstantInterface $instant
     * @return LocalTime
     */
    public function convertToLocal(InstantInterface $instant)
    {
        return LocalTime::convert($this->getTimeZone(), $instant);
    }

    /**
     * Parses data in any format and returns instant
     *
     * @param int|float|string|Timestamp|\DateTimeInterface $value
     * @return InstantInterface
     * @throws \InvalidArgumentException
     */
    public function parse($value)
    {
        switch (true) {
            case $value instanceof InstantInterface:
                return new Timestamp($value->toFloat());
            case $value instanceof \DateTimeInterface:
                return new Timestamp($value->getTimestamp());
            case is_int($value):
            case is_float($value):
                return new Timestamp($value);
            case is_string($value):
                return $this->strToTime($value);
            default:
                throw new \InvalidArgumentException('Unknown format received');
        }
    }

    /**
     * Parses date time, packed in string
     *
     * @param string $value
     * @return InstantInterface
     * @throws \InvalidArgumentException
     */
    public function strToTime($value)
    {
        if (!is_string($value)) {
            throw new NotStringException('value');
        }

        try {
            return $this->parse(new \DateTime($value, $this->getTimeZone()));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                'Unable to parse time string ' . $value,
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Returns instant, formatted with provided format
     *
     * @param InstantInterface $instant
     * @param string           $format
     * @return string
     */
    public function format(InstantInterface $instant, $format)
    {
        return $instant->toDateTime()
                       ->setTimezone($this->getTimeZone())
                       ->format($format);
    }

    /**
     * Returns mysql date time representation of instant
     *
     * @param InstantInterface $instant
     * @return string
     */
    public function formatMysqlDateTime(InstantInterface $instant)
    {
        return $this->format($instant, 'Y-m-d H:i:s');
    }

    /**
     * Returns mysql date representation if instant
     *
     * @param InstantInterface $instant
     * @return string
     */
    public function formatMysqlDate(InstantInterface $instant)
    {
        return $this->format($instant, 'Y-m-d');
    }
}
