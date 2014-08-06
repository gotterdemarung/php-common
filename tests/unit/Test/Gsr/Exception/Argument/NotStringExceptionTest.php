<?php

namespace Test\Gsr\Exception\Argument;

use Gsr\Exception\Argument\NotStringException;

class NotStringExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        $x = new NotStringException('fooBar');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Argument `fooBar` supposed to be a string', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }

    public function testConstructorWithMessage()
    {
        $x = new NotStringException('fooBar', 'Some text');
        $this->assertSame('fooBar', $x->getArgumentName());
        $this->assertSame('Some text', $x->getMessage());
        $this->assertSame(0, $x->getCode());
        $this->assertNull($x->getPrevious());
    }
} 