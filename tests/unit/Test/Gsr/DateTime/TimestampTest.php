<?php

namespace Test\Gsr\DateTime;

use Gsr\DateTime\Timestamp;

class TimestampTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider wrongArgs
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWrongArguments($arg)
    {
        new Timestamp($arg);
    }

    public function wrongArgs()
    {
        return [
            [null],
            [true],
            [false],
            [new \stdClass()],
            [[]],
            [[15]],
            ["52"],
            ['abc']
        ];
    }

    public function testBehaviour()
    {
        date_default_timezone_set('UTC');
        $x = new Timestamp(123456789);

        $this->assertSame(123456789.0, $x->toFloat());
        $this->assertSame(123456789, $x->toInt());
        $this->assertSame(123456789, $x->toDateTime()->getTimestamp());

        $x = new Timestamp(123.456789);

        $this->assertSame(123.456789, $x->toFloat());
        $this->assertSame(123, $x->toInt());
        $this->assertSame(123, $x->toDateTime()->getTimestamp());
    }

    /**
     * @dataProvider wrongArgs
     */
    public function testEqualsWrong($arg)
    {
        $x = new Timestamp(1.23);

        $this->assertFalse($x->equals($arg));
    }

    public function testEquals()
    {
        $x = new Timestamp(1.23);

        $this->assertTrue($x->equals(1.23));
        $this->assertTrue($x->equals(new Timestamp(1.23)));
        $this->assertTrue($x->equals($x));

        $this->assertFalse($x->equals(1.22));
        $this->assertFalse($x->equals(1.24));
        $this->assertFalse($x->equals(1));
        $this->assertFalse($x->equals(2));
    }
} 