<?php

namespace Gsr\Validation\Common;

use Gsr\Validation\CommonValidator;
use Gsr\Validation\Result\SimpleError;
use Gsr\Validation\Result\SimpleSuccess;
use Gsr\Validation\ValidationResultInterface;

class FloatWeakValidator extends CommonValidator
{
    /**
     * Returns true if argument is integer, float or double
     *
     * @param mixed $mixed
     * @return ValidationResultInterface
     */
    public function validate($mixed)
    {
        return is_int($mixed) || is_float($mixed) || is_double($mixed)
             ? new SimpleSuccess()
             : new SimpleError("Value is not float, double or int");
    }
}
