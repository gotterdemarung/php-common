<?php

namespace Gsr\Validation;

/**
 * Validation result, returned by validators
 *
 * @package Gsr\Validation
 */
interface ValidationResultInterface
{
    /**
     * Returns true, if validation done without errors
     *
     * @return boolean
     */
    public function isSuccess();

    /**
     * Returns list of errors
     *
     * @return \Exception[]
     */
    public function getErrors();
}
