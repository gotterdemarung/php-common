<?php

namespace Gsr\Validation\Result;

use Gsr\Validation\ValidationResultInterface;

class SimpleSuccess implements ValidationResultInterface
{
    /**
     * Returns true, if validation done without errors
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * Returns list of errors
     *
     * @return \Exception[]
     */
    public function getErrors()
    {
        return [];
    }
}
