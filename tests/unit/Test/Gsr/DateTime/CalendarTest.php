<?php

namespace Test\Gsr\DateTime;

use Gsr\DateTime\Calendar;
use Gsr\DateTime\Timestamp;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleton()
    {
        $x = Calendar::getUTC();
        $y = Calendar::getUTC();
        $z = Calendar::getUTC();

        $this->assertEquals('UTC', $x->getTimeZone()->getName());
        $this->assertSame($x, $y);
        $this->assertSame($x, $z);
        $this->assertSame($z, $y);
    }

    public function testConstructor()
    {
        $x = new Calendar(new \DateTimeZone('UTC'));
        $y = new Calendar(new \DateTimeZone('Europe/Kiev'));

        $this->assertEquals('UTC', $x->getTimeZone()->getName());
        $this->assertEquals('Europe/Kiev', $y->getTimeZone()->getName());
    }

    public function testGetCurrent()
    {
        date_default_timezone_set('UTC');
        $now = time();

        $x = new Calendar(new \DateTimeZone('UTC'));
        $y = new Calendar(new \DateTimeZone('Europe/Kiev'));

        $this->assertLessThan(1, $x->getCurrent()->toFloat() - $now);
        $this->assertLessThan(1, $y->getCurrent()->toFloat() - $now);
        $this->assertTrue($x->getCurrent() instanceof Timestamp);
        $this->assertTrue($y->getCurrent() instanceof Timestamp);
    }

    public function testParse()
    {
        $x = new Calendar(new \DateTimeZone('UTC'));
        $y = new Calendar(new \DateTimeZone('Europe/Kiev'));

        $this->assertSame(12345, $x->parse(12345)->toInt());
        $this->assertSame(12345, $y->parse(12345.678)->toInt());
        $this->assertSame(1371035373, $x->parse('2013-06-12 11:09:33')->toInt());
        $this->assertSame(1371024573, $y->parse('2013-06-12 11:09:33')->toInt());
        $this->assertSame(12345, $x->parse(new Timestamp(12345))->toInt());
        $this->assertSame(12, $x->parse(new \DateTime('@12'))->toInt());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseError()
    {
        Calendar::getUTC()->parse('foo');
    }

    public function testFormat()
    {
        $x = new Calendar(new \DateTimeZone('UTC'));
        $y = new Calendar(new \DateTimeZone('Europe/Kiev'));

        $this->assertSame('12/06/13 08:09:33', $x->format($x->parse(1371024573), 'd/m/y H:i:s'));
        $this->assertSame('12/06/13 11:09:33', $y->format($y->parse(1371024573), 'd/m/y H:i:s'));
    }

    public function testFormatMysqlDateTime()
    {
        $x = new Calendar(new \DateTimeZone('UTC'));
        $y = new Calendar(new \DateTimeZone('Europe/Kiev'));

        $this->assertSame('2013-06-12 08:09:33', $x->formatMysqlDateTime($x->parse(1371024573)));
        $this->assertSame('2013-06-12 11:09:33', $y->formatMysqlDateTime($y->parse(1371024573)));
    }

    public function testFormatMysqlDate()
    {
        $x = new Calendar(new \DateTimeZone('UTC'));

        $this->assertSame('2013-06-12', $x->formatMysqlDate($x->parse(1371024573)));
    }
}