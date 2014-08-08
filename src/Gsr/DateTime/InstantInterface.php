<?php

namespace Gsr\DateTime;

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
}
