<?php

namespace Test\Gsr\Exception\Argument;

use Gsr\Exception\Argument\EmptyArgumentException;

class EmptyArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        $x = new EmptyArgumentException('fooBar');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Empty argument `fooBar` received', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }

    public function testConstructorWithMessage()
    {
        $x = new EmptyArgumentException('fooBar', 'Some text');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Some text', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }
} 