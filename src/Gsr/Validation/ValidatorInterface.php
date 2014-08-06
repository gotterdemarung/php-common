<?php

namespace Gsr\Validation;

interface ValidatorInterface
{
    /**
     * Applies validator on provided argument and returns result
     *
     * @param mixed $mixed
     * @return ValidationResultInterface
     */
    public function validate($mixed);
}
