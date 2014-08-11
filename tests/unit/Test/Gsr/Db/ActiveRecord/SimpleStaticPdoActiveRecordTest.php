<?php

namespace Test\Gsr\Db\ActiveRecord;

use Gsr\Db\ActiveRecord\SimpleStaticPdoActiveRecord;

class SimpleStaticPdoActiveRecordTest extends \PHPUnit_Framework_TestCase
{
    protected function getPdo()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query('CREATE TABLE `impl1_table`(`id` integer PRIMARY KEY, `name` text, `cnt` int);');
        $pdo->query('CREATE TABLE `implX_table`(`notId` integer PRIMARY KEY, `date` text);');
        $pdo->query('INSERT INTO `impl1_table` VALUES (null, "hello", 542);');
        $pdo->query('INSERT INTO `impl1_table` VALUES (null, "foo", -4);');
        $pdo->query('INSERT INTO `implX_table` VALUES (null, "2014-02-16");');
        $pdo->query('INSERT INTO `implX_table` VALUES (null, "2014-11-22 16:11:03");');
        $pdo->query('INSERT INTO `implX_table` VALUES (null, null);');

        return $pdo;
    }

    public function testFindAllBySql()
    {
        Impl1::setPdoConnection($this->getPdo());

        $result = Impl1::findAllBySql('SELECT * FROM :: WHERE `name` = "foo"');
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof Impl1);
        $this->assertSame('foo', $result[0]['name']);
        $this->assertEquals(-4, $result[0]['cnt']);

        $result = Impl1::findAllBySql('SELECT * FROM :: WHERE `name` = :x', ['x' => 'hello']);
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]->getId());

        $result = Impl1::findAllBySql('SELECT * FROM :: ORDER BY `id` ASC');
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->getId());
        $this->assertEquals(2, $result[1]->getId());

        $result = Impl1::findAllBySql('SELECT * FROM :: WHERE `name` = "no"');
        $this->assertTrue(is_array($result));
        $this->assertCount(0, $result);
    }

    public function testFindAllBySqlException()
    {
        Impl1::setPdoConnection($this->getPdo());
        foreach ([true, false, 0, -5, .2, new \stdClass(), []] as $value) {
            try {
                Impl1::findAllBySql($value);
                $this->fail('Expecting exception on value ' . print_r($value, true));
            } catch (\InvalidArgumentException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function testFindAllByAttributes()
    {
        Impl1::setPdoConnection($this->getPdo());

        $result = Impl1::findAllByAttributes(['name' => 'foo']);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof Impl1);
        $this->assertEquals(2, $result[0]->getId());
        $this->assertSame('foo', $result[0]['name']);
        $this->assertEquals(-4, $result[0]['cnt']);

        $result = Impl1::findAllByAttributes([]);
        $this->assertTrue(is_array($result));
        $this->assertCount(0, $result);
    }

    public function testFindById()
    {
        Impl1::setPdoConnection($this->getPdo());
        Impl2::setPdoConnection($this->getPdo());

        $this->assertEquals('hello', Impl1::findById(1)->offsetGet('name'));
        $this->assertEquals('foo', Impl1::findById(2)->offsetGet('name'));
        $this->assertEquals('2014-11-22 16:11:03', Impl2::findById(2)->offsetGet('date'));
    }

    /**
     * @depends testFindById
     */
    public function testIsNewRecord()
    {
        Impl1::setPdoConnection($this->getPdo());
        $this->assertFalse(Impl1::findById(2)->isNewRecord());

        $x = new Impl1(['name' => 'third']);
        $this->assertTrue($x->isNewRecord());
    }

    public function testSave()
    {
        Impl1::setPdoConnection($this->getPdo());
        $x = Impl1::findById(2);
        $this->assertSame('foo', $x['name']);

        $x['name'] = 'bar';
        $x->save();
        $this->assertSame('bar', $x['name']);
        $x = Impl1::findById(2);
        $this->assertSame('bar', $x['name']);

        $x = new Impl1([]);
        $x['name'] = 'baz332222';
        $x->save();

        $x = Impl1::findById(3);
        $this->assertEquals('baz332222', $x['name']);

        $this->assertCount(3, Impl1::findAllBySql('SELECT * FROM ::'));
    }

    public function testSaveOnlyChangesFields()
    {
        $pdo = $this->getPdo();
        Impl1::setPdoConnection($pdo);

        $x = Impl1::findById(2);

        // Changing name in AR, and cnt manually
        $x['name'] = 'changedOne';
        $pdo->query('UPDATE impl1_table SET `cnt` = 9901 WHERE id = 2');
        $x->save();

        $x = Impl1::findById(2);
        $this->assertEquals('changedOne', $x['name']);
        $this->assertEquals(9901, $x['cnt']);
    }

    public function testArMagicMethods()
    {
        // NO PDO NEEDED
        $x = new Impl1([
            'int'      => '3524',
            'float'    => '2.222',
            'string'   => 'foooooooo',
            'date'     => '2012-08-13',
            'dateTime' => '1998-06-14 13:33:22',
            'empty'    => '',
            'zero'     => '0',
            'null'     => null
        ]);

        $this->assertSame('2012-08-13', $x->date);
        $this->assertSame('2012-08-13', $x->offsetGet('date'));
        $this->assertSame('2012-08-13', $x['date']);

        $x->offsetSet('foo', 'z1');
        $this->assertSame('z1', $x['foo']);

        $x['foo'] = 'z2';
        $this->assertSame('z2', $x['foo']);

        $x->offsetSet('foo', 'z3');
        $this->assertSame('z3', $x['foo']);

        unset($x['foo']);
        try {
            $y = $x['foo'];
            $this->fail('Exception expected');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        $this->assertSame(3524, $x->intval('int'));
        $this->assertSame(2, $x->intval('float'));
        $this->assertSame(0, $x->intval('string'));
        $this->assertSame(0, $x->intval('null'));

        $this->assertSame(3524.0, $x->floatval('int'));
        $this->assertSame(2.222, $x->floatval('float'));
        $this->assertSame(.0, $x->floatval('string'));
        $this->assertSame(.0, $x->floatval('null'));

        $this->assertSame(1344816000, $x->timeval('date')->toInt());
        $this->assertSame(897831202, $x->timeval('dateTime')->toInt());
        $this->assertNull($x->timeval('zero'));
        $this->assertNull($x->timeval('null'));
        $this->assertNull($x->timeval('empty'));
    }

    public function testGetCollection()
    {
        $x = new Impl1([]);
        $this->assertSame(Impl1::TABLE, $x->getCollection());
    }
}

class Impl1 extends SimpleStaticPdoActiveRecord
{
    const TABLE = 'impl1_table';

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function intval($x)
    {
        return $this->castToInt($x);
    }

    public function floatval($x)
    {
        return $this->castToFloat($x);
    }

    public function timeval($x)
    {
        return $this->castToInstant($x);
    }
}

class Impl2 extends SimpleStaticPdoActiveRecord
{
    const TABLE = 'implX_table';
    const PRIMARY_KEY = 'notID';
}
