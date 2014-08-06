<?php

namespace Gsr\Validation\Common;

use Gsr\Validation\CommonValidator;
use Gsr\Validation\Result\SimpleError;
use Gsr\Validation\Result\SimpleSuccess;
use Gsr\Validation\ValidationResultInterface;

class EmptinessValidator extends CommonValidator
{
    /**
     * Applies validator on provided argument and returns result
     *
     * @param mixed $mixed
     * @return ValidationResultInterface
     */
    public function validate($mixed)
    {
        if (empty($mixed)) {
            return new SimpleError("Value is empty");
        } elseif (is_string($mixed) && trim($mixed) === '') {
            return new SimpleError("Value is empty");
        }

        return new SimpleSuccess();
    }
}
