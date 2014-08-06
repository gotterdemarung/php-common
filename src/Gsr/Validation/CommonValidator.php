<?php

namespace Gsr\Validation;

/**
 * Abstract singleton & utility method holder
 *
 * @package Gsr\Validation
 */
abstract class CommonValidator implements ValidatorInterface
{
    private static $instances = [];

    /**
     * Returns validator instance
     *
     * @return ValidatorInterface
     */
    final public static function getInstance()
    {
        $cls = \get_called_class();
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }

        return self::$instances[$cls];
    }
}
