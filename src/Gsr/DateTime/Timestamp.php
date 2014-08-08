<?php

namespace Gsr\DateTime;

/**
 * UTC time holder with microseconds precision
 *
 * @package Gsr\DateTime
 */
class Timestamp implements InstantInterface
{
    /**
     * @var float
     */
    private $timestamp;

    /**
     * Constructor
     *
     * @param int|float $timestamp
     * @throws \InvalidArgumentException
     */
    public function __construct($timestamp)
    {
        if (is_int($timestamp) || is_float($timestamp)) {
            $this->timestamp = (float) $timestamp;
        } else {
            throw new \InvalidArgumentException(
                'Provided timestamp is not valid. Null, float and integer acceptable'
            );
        }
    }

    /**
     * Returns float representation
     *
     * @return float
     */
    public function toFloat()
    {
        return $this->timestamp;
    }

    /**
     * Returns integer representation (unix timestamp)
     *
     * @return int
     */
    public function toInt()
    {
        return intval($this->timestamp);
    }

    /**
     * Return PHP DateTime object
     *
     * @return \DateTime
     */
    public function toDateTime()
    {
        return new \DateTime('@' . $this->toInt());
    }

    /**
     * Returns true, if timestamp of instant equals provided one
     *
     * @param int|float|Timestamp $value
     * @return bool
     */
    public function equals($value)
    {
        switch (true) {
            case $value === null:
                return false;
            case is_int($value):
            case is_float($value):
                return $value == $this->toFloat();
            case $value instanceof Timestamp:
                return $value->toFloat() === $this->toFloat();
            default:
                return false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function __toString()
    {
        return '' . $this->toInt();
    }
}
