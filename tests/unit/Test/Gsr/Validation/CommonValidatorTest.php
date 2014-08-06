<?php

namespace Test\Gsr\Validation;

use Gsr\Validation\Common\FloatWeakValidator;
use Gsr\Validation\Common\IntegerValidator;
use Gsr\Validation\Common\StringValidator;

class CommonValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testSingletonHolder()
    {
        $x1 = IntegerValidator::getInstance();
        $y1 = FloatWeakValidator::getInstance();
        $z1 = StringValidator::getInstance();
        $x2 = IntegerValidator::getInstance();
        $y2 = FloatWeakValidator::getInstance();
        $z2 = StringValidator::getInstance();

        $this->assertNotSame($x1, $y1);
        $this->assertNotSame($x1, $z1);
        $this->assertNotSame($y1, $z1);

        $this->assertNotSame($x2, $y2);
        $this->assertNotSame($x2, $z2);
        $this->assertNotSame($y2, $z2);

        $this->assertSame($x1, $x2);
        $this->assertSame($y1, $y2);
        $this->assertSame($z1, $z2);
    }
} 