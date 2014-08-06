<?php

namespace Gsr\Exception\Argument;

use Gsr\Exception\ArgumentException;

class EmptyArgumentException extends ArgumentException
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
            empty($message) ? "Empty argument `{$argumentName}` received" : $message
        );
    }
}
