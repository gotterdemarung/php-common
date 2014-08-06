<?php

namespace Test\Gsr\Exception\Argument;

use Gsr\Exception\Argument\NotIntegerException;

class NotIntegerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        $x = new NotIntegerException('fooBar');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Argument `fooBar` supposed to be a integer', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }

    public function testConstructorWithMessage()
    {
        $x = new NotIntegerException('fooBar', 'Some text');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Some text', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }
} 