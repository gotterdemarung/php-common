<?php

namespace Gsr\Exception\Argument;

use Gsr\Exception\ArgumentException;

class NotStringException extends ArgumentException
{
    /**
     * Constructor
     *
     * @param string            $argumentName
     * @param string (optional) $message
     */
    public function __construct($argumentName, $message = null)
    {
        parent::__construct(
            $argumentName,
            empty($message) ? "Argument `{$argumentName}` supposed to be a string" : $message
        );
    }
}
