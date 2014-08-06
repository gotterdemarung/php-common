<?php

namespace Test\Gsr\Validation\Common;

use Gsr\Validation\Common\EmptinessValidator;

class EmptinessValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $x = new EmptinessValidator();

        $this->assertFalse($x->validate(null)->isSuccess());
        $this->assertFalse($x->validate(false)->isSuccess());
        $this->assertFalse($x->validate(0)->isSuccess());
        $this->assertFalse($x->validate([])->isSuccess());
        $this->assertFalse($x->validate('')->isSuccess());
        $this->assertFalse($x->validate('   ')->isSuccess());

        $this->assertTrue($x->validate(true)->isSuccess());
        $this->assertTrue($x->validate(-5)->isSuccess());
        $this->assertTrue($x->validate(.00001)->isSuccess());
        $this->assertTrue($x->validate('x')->isSuccess());
    }
} 