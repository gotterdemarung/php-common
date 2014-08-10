<?php

namespace Gsr\DateTime;

/**
 * Represents time point
 *
 * @package Gsr\DateTime
 */
interface InstantInterface
{
    /**
     * Returns float representation
     *
     * @return float
     */
    public function toFloat();

    /**
     * Returns integer representation (unix timestamp)
     *
     * @return int
     */
    public function toInt();

    /**
     * Return PHP DateTime object
     *
     * @return \DateTime
     */
    public function toDateTime();

    /**
     * Returns true if timestamp value of instant equals to provided one
     *
     * @param mixed $value
     * @return boolean
     */
    public function equals($value);
}
