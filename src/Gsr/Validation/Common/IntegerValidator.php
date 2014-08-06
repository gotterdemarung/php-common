<?php

namespace Gsr\Validation\Common;

use Gsr\Validation\CommonValidator;
use Gsr\Validation\Result\SimpleError;
use Gsr\Validation\Result\SimpleSuccess;
use Gsr\Validation\ValidationResultInterface;

class IntegerValidator extends CommonValidator
{
    /**
     * Applies validator on provided argument and returns result
     *
     * @param mixed $mixed
     * @return ValidationResultInterface
     */
    public function validate($mixed)
    {
        return is_int($mixed) ? new SimpleSuccess() : new SimpleError("Value is not integer");
    }
}
