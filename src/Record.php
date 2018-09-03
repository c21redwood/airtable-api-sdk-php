<?php
namespace Airtable;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use Airtable\Facades\Facade;
use Airtable\Contracts\Driver;
use Illuminate\Contracts\Support\Arrayable;

class Record implements \ArrayAccess, Arrayable, Jsonable {

  /**
   * @var string
   */
  protected $connection = null;

  /**
   * @var Driver
   */
  protected $driver;

  /**
   * @var Driver
   */
  protected $client = null;

  /**
   * @var array
   */
  protected $fields = [];

  /**
   * @var string
   */
  protected $id = null;

  /**
   * @var string
   */
  protected $tableName = null;

  /**
   * @var array
   */
  protected $dates = [];

  /**
   * @var array
   */
  protected $casts = [];

  /**
   * Record constructor.
   * @param Driver $client
   * @param array $fields
   */
  function __construct(array $fields = [])
  {
    if ($fields) {
      $this->fill($fields);
    }
  }

  /**
   * Set the ID for this Record
   * @param string $id
   * @return $this
   */
  function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  function getId()
  {
    return $this->id;
  }

  function getTableName()
  {
    if (empty($this->tableName)) {
      $reflect = new ReflectionClass($this);
      $this->tableName = trim(preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $reflect->getShortName()));
    }
    return $this->tableName;
  }

  function setTableName($tableName)
  {
    $this->tableName = $tableName;

    return $this;
  }

  function setDriver(Driver $driver)
  {
    $this->driver = $driver;

    return $this;
  }

  function getDriver()
  {
    if (empty($this->driver)) {
      $this->driver = Facade::connection($this->connection);
    }
    return $this->driver;
  }

  function save()
  {
    return $this->getId() ? $this->getDriver()->update($this) : $this->getDriver()->create($this);
  }

  /**
   * @throws AirtableException When attempting to delete a record that hasn't been saved
   */
  function delete()
  {
    if (!$id = $this->getId()) {
      throw new AirtableException("Can't delete a record that hasn't been saved yet");
    }

    return $this->getDriver()->delete($this->getTableName(), $id);
  }

  function fill(array $fields)
  {
    foreach($fields as $name => $value) {
      $this[$name] = $value;
    }

    return $this;
  }

  function toArray()
  {
    return $this->fields;
  }

  function toJson($options = 0)
  {
    return json_encode((object) [
      'fields' => $this->toArray()
    ], $options);
  }

  protected function cast($value, $type)
  {
    switch($type) {
      case 'date':
        return Carbon::parse($value)->format('Y-m-d');
      case 'dateTime':
        return Carbon::parse($value)->format('c');
      case 'bool':
      case 'boolean':
        return (bool) $value;
      default:
        throw new \InvalidArgumentException("Unrecognized cast: {$type}");
    }
  }

  function __set($name, $value)
  {
    $this->fields[$name] = $this->parse($name, $value);

    return $this;
  }

  protected function parse($name, $value)
  {
    if (is_array($value)) {
      $return = [];
      foreach($value as $v) {
        $return[] = $this->parse($name, $v);
      }
      return $return;
    } else if ($value instanceof Collaborator) {
      return $value->toArray();
    } else if ($value instanceof Collaborator) {
      return $value->toArray();
    } else if ($value instanceof Attachment) {
      return $value->toArray();
    } else if ($value instanceof Carbon) {
      return $value->format('Y-m-d');
    } else if (in_array($name, $this->dates)) {
      return Carbon::parse($value)->format('Y-m-d');
    } else if (in_array($name, $this->casts)) {
      return $this->cast($value, $this->casts[$name]);
    } else {
      return $value;
    }

  }

  function __get($name)
  {
    if (isset($this->fields[$name])) {
      $value = $this->fields[$name];

      if (in_array($name, $this->dates)) {
        $value = Carbon::parse($value);
      }

      return $value;
    }

    return null;
  }

  public function offsetExists($offset)
  {
    return isset($this->fields[$offset]);
  }

  public function offsetGet($offset)
  {
    if ($offset === 'id') {
      return $this->getId();
    } else {
      return $this->__get($offset);
    }
  }

  public function offsetSet($offset, $value)
  {
    if ($offset === 'id') {
      return $this->setId('id', $value);
    } else {
      return $this->__set($offset, $value);
    }
  }

  public function offsetUnset($offset)
  {
    unset($this->fields[$offset]);
  }
}