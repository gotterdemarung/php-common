<?php

namespace Gsr\Db\ActiveRecord;

use Gsr\DateTime\Calendar;
use Gsr\DateTime\InstantInterface;
use Gsr\Exception\Argument\NotStringException;

/**
 * Usecase:
 * Extend this class with any amount of your instances, in each one declare constant TABLE and set table name
 * On demand you can redeclare constant PRIMARY_KEY (by default points to `id`)
 * After that, call once SimpleStaticPdoActiveRecord::setPdo to provide PDO connection to all instances
 *
 * All instances of this class are active records
 *
 * This class is antipattern in common, and must be used wisely in short-run application
 *
 * Do not design your application using this (or any other) active record implementation
 *
 * @package Gsr\Db\ActiveRecord
 */
abstract class SimpleStaticPdoActiveRecord implements \ArrayAccess
{
    // Table name. You MUST override it
    const TABLE = null;
    // Primary key name. You CAN override it
    const PRIMARY_KEY = 'id';

    /**
     * Php Data Object, used by all instances of active record
     *
     * @var \PDO
     */
    private static $pdo = null;

    /**
     * Pool of buffered objects
     *
     * @var SimpleStaticPdoActiveRecord[]
     */
    private static $idBuffer = array();

    /**
     * Active record content
     *
     * @var array
     */
    private $data = array();

    /**
     * Active record changes
     *
     * @var array
     */
    private $changes = array();

    /**
     * Set main PDO connection
     *
     * @param \PDO $pdo
     * @return void
     */
    public static function setPdoConnection(\PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * Returns PDO connection for table
     *
     * @return \PDO
     * @throws \LogicException
     */
    public static function getPdoConnection()
    {
        if (self::$pdo === null) {
            throw new \LogicException('No PDO object provided to active record');
        } else {
            return self::$pdo;
        }
    }

    /**
     * Finds all records for SQL
     * Replaces :: to table name
     *
     * @param string $sql
     * @param array  $params
     *
     * @return static[]
     * @throws \InvalidArgumentException
     */
    public static function findAllBySql($sql, array $params = array())
    {
        if (!is_string($sql)) {
            throw new NotStringException('sql');
        }

        // Replacing table placeholder
        $sql = str_replace('::', '`' . static::TABLE . '`', $sql);

        $stmt = static::getPdoConnection()->prepare($sql);
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        $pool = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pool[] = new static($row);
        }

        return $pool;
    }

    /**
     * Finds entries by attributes
     *
     * @param array $attributes
     * @return static[]
     */
    public static function findAllByAttributes(array $attributes)
    {
        if (count($attributes) === 0) {
            return [];
        }

        $sql = 'SELECT * FROM :: WHERE 1 ';
        $params = array();
        foreach ($attributes as $key => $value) {
            $sql .= " AND `{$key}` = :{$key}";
            $params[':' . $key] = $value;
        }

        return static::findAllBySql($sql, $params);
    }

    /**
     * Finds entry by ID
     *
     * @param int $id
     * @return static Or null
     */
    public static function findById($id)
    {
        $results = static::findAllBySql(
            'SELECT * FROM :: WHERE `'
            . static::PRIMARY_KEY
            . '` = :id LIMIT 1',
            array(':id' => $id)
        );
        if (count($results) != 1) {
            return null;
        } else {
            return $results[0];
        }
    }

    /**
     * Finds entry by ID and bufferizes it for script execution time
     *
     * @param int $id
     * @return null|static
     */
    public static function findByIdBuffered($id)
    {
        $key = get_called_class() . '-' . $id;
        if (!array_key_exists($key, self::$idBuffer)) {
            self::$idBuffer[$key] = self::findById($id);
        }

        return self::$idBuffer[$key];
    }

    /**
     * Protected constructor
     *
     * @param array $initial
     */
    protected function __construct($initial = array())
    {
        $this->data = $initial;
    }

    /**
     * Magic getter
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Magic setter
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . ' #' . $this->getId();
    }

    /**
     * Returns integer value
     *
     * @param string $offset
     * @return int
     */
    protected function castToInt($offset)
    {
        return (int) $this[$offset];
    }

    /**
     * Returns float value
     *
     * @param string $offset
     * @return float
     */
    protected function castToFloat($offset)
    {
        return (float) $this[$offset];
    }

    /**
     * Returns instant
     *
     * @param string $offset
     * @return InstantInterface|null
     */
    protected function castToInstant($offset)
    {
        if ($this->offsetGet($offset) == '' || $this->offsetGet($offset) == 0) {
            return null;
        }
        return Calendar::getUTC()->parse($this[$offset]);
    }

    /**
     * Returns true, if current active record does not present in db and supposed
     * to be stored using INSERT statement
     *
     * @return bool
     */
    public function isNewRecord()
    {
        return !isset($this->data[static::PRIMARY_KEY]);
    }

    /**
     * Returns database table name
     *
     * @return string
     */
    public function getCollection()
    {
        return static::TABLE;
    }

    /**
     * Returns ID of entry
     *
     * @return int
     */
    public function getId()
    {
        return $this->data[static::PRIMARY_KEY];
    }

    /**
     * Returns true if active record contains value
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Returns value
     *
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        if (!isset($this[$offset])) {
            throw new \InvalidArgumentException(
                "$offset not found in " . get_class($this)
            );
        }

        return $this->data[$offset];
    }

    /**
     * Sets value
     *
     * @param string $offset
     * @param mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->changes[$offset] = $value;
        $this->data[$offset]    = $value;
    }

    /**
     * Unset value
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->changes[$offset]);
        unset($this->data[$offset]);
    }

    /**
     * Saves changes for active record
     *
     * @return void
     */
    public function save()
    {
        if (count($this->changes) !== 0) {
            if ($this->isNewRecord()) {
                // Inserting
                $table  = $this->getCollection();
                $keys   = implode(
                    ', ',
                    array_map(
                        function ($x) {
                            return "`$x`";
                        },
                        array_keys($this->changes)
                    )
                );
                $values = array_values($this->changes);
                $ph     = implode(', ', array_fill(0, count($values), '?'));
                $stmt = $this->getPdoConnection($table)->prepare(
                    "INSERT INTO {$table} ($keys) VALUES ({$ph})"
                );
                $stmt->execute($values);
                $this[self::PRIMARY_KEY] = $this->getPdoConnection($table)->lastInsertId();
            } else {
                // UPDATING
                $table  = $this->getCollection();
                $keys   = array_map(
                    function ($x) {
                        return "`$x`";
                    },
                    array_keys($this->changes)
                );
                $values = array_values($this->changes);
                $ph     = array_map(
                    function ($x) {
                        return ':' . $x;
                    },
                    array_keys($this->changes)
                );
                $total  = array();
                // JOINING
                for ($i = 0; $i < count($keys); $i++) {
                    $total[] = $keys[$i] . ' = ' . $ph[$i];
                }
                $total  = implode(', ', $total);
                $pk     = self::PRIMARY_KEY;
                $stmt = $this->getPdoConnection($table)->prepare(
                    "UPDATE {$table} SET {$total} WHERE `{$pk}` = :{$pk} LIMIT 1"
                );
                $replacements = array_combine($ph, $values);
                $replacements[$pk] = $this->getId();
                foreach ($replacements as $k => $v) {
                    $stmt->bindValue($k, $v);
                }
                $stmt->execute();
            }
        }

        $this->changes = array();
    }


    /**
     * Returns true if current object has same class, as {@see $another},
     * and their IDs are same
     *
     * @param SimpleStaticPdoActiveRecord $another
     * @return bool
     */
    public function equals(SimpleStaticPdoActiveRecord $another)
    {
        return get_class($this) == get_class($another)
        && $this->getId() == $another->getId();
    }
}
