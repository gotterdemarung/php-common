<?php

namespace Test\Gsr\Validation\Common;

use Gsr\Validation\Common\FloatWeakValidator;

class FloatWeakValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $x = new FloatWeakValidator();

        $this->assertFalse($x->validate(null)->isSuccess());
        $this->assertFalse($x->validate(false)->isSuccess());
        $this->assertFalse($x->validate([])->isSuccess());
        $this->assertFalse($x->validate('')->isSuccess());
        $this->assertFalse($x->validate('   ')->isSuccess());

        $this->assertTrue($x->validate(-5)->isSuccess());
        $this->assertTrue($x->validate(.1)->isSuccess());
    }
} 