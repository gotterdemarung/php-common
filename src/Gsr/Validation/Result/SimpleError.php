<?php

namespace Gsr\Validation\Result;

use Gsr\Validation\ValidationResultInterface;

class SimpleError implements ValidationResultInterface
{
    /**
     * @var \Exception[]
     */
    private $cause;

    /**
     * Constructor
     *
     * @param string|\Exception $cause
     */
    public function __construct($cause)
    {
        $this->cause = [is_string($cause) ? new \Exception($cause) : $cause];
    }

    /**
     * Returns true, if validation done without errors
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return false;
    }

    /**
     * Returns list of errors
     *
     * @return \Exception[]
     */
    public function getErrors()
    {
        return $this->cause;
    }
}
