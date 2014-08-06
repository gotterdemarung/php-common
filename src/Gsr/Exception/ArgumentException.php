<?php

namespace Gsr\Exception;

/**
 * Exception, to be thrown when argument validation failed
 *
 * @package Gsr\Exception
 */
class ArgumentException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private $argumentName;

    /**
     * Constructor
     *
     * @param string            $argumentName
     * @param string (optional) $message
     */
    public function __construct($argumentName, $message = null)
    {
        parent::__construct(
            empty($message) ? "Invalid argument {$argumentName}" : $message
        );
        $this->argumentName = $argumentName;
    }

    /**
     * Returns argument name, caused an exception
     *
     * @return string
     */
    public function getArgumentName()
    {
        return $this->argumentName;
    }
}
