<?php

namespace Test\Gsr\DateTime;

use Gsr\DateTime\LocalTime;
use Gsr\DateTime\Timestamp;

class LocalTimeTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $x = LocalTime::convert(new \DateTimeZone('UTC'), new Timestamp(1356024123));
        $y = LocalTime::convert(new \DateTimeZone('Europe/Kiev'), new Timestamp(1356024123));

        $this->assertEquals('UTC', $x->getTimezone()->getName());
        $this->assertEquals('Europe/Kiev', $y->getTimezone()->getName());
        $this->assertSame($x->getYear(), $y->getYear());
        $this->assertSame($x->getMonth(), $y->getMonth());
        $this->assertSame($x->getDay(), $y->getDay());
        $this->assertSame($x->getHour(), $y->getHour());
        $this->assertSame($x->getMinute(), $y->getMinute());
        $this->assertSame($x->getSecond(), $y->getSecond());
        $this->assertSame(7200, $x->toInt() - $y->toInt());
    }

    public function testBehaviour()
    {
        $tz = new \DateTimeZone('UTC');
        $x = new LocalTime($tz, 2012, 12, 20, 17, 22, 03, 65321);

        $this->assertSame($tz, $x->getTimezone());
        $this->assertSame(2012, $x->getYear());
        $this->assertSame(12, $x->getMonth());
        $this->assertSame(20, $x->getDay());
        $this->assertSame(17, $x->getHour());
        $this->assertSame(22, $x->getMinute());
        $this->assertSame(03, $x->getSecond());
        $this->assertSame(65321, $x->getMicroseconds());
        $this->assertSame(1356024123, $x->toInt());
        $this->assertSame(1356024123.065321, $x->toFloat());
        $this->assertSame(1356024123, $x->toDateTime()->getTimestamp());

        $tz = new \DateTimeZone('Europe/Kiev');
        $x = new LocalTime($tz, 2012, 12, 20, 17, 22, 03, 65321);
        $this->assertSame(1356016923, $x->toInt());
    }

    public function testEquals()
    {
        $tz = new \DateTimeZone('UTC');
        $x = new LocalTime($tz, 2012, 12, 20, 17, 22, 03, 65321);

        $tz = new \DateTimeZone('Europe/Kiev');
        $y = new LocalTime($tz, 2012, 12, 20, 19, 22, 03, 65321);

        $this->assertTrue($x->equals(1356024123.065321));
        $this->assertFalse($x->equals(1356024123));
        $this->assertTrue($x->equals($x));
        $this->assertTrue($x->equals($y));
    }
} 