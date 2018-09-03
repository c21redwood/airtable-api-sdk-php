<?php
namespace Airtable\Managers;

use Closure;
use InvalidArgumentException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class AbstractManager {

  /**
   * The active connection instances.
   *
   * @var array
   */
  protected $connections = [];

  protected $app;

  protected $configName;

  /**
   * The registered custom driver creators.
   *
   * @var array
   */
  protected $customCreators = [];

  public function __construct($app)
  {
    $this->app = $app;
  }

  /**
   * Register a custom driver creator Closure.
   *
   * @param  string    $driver
   * @param  \Closure  $callback
   * @return $this
   */
  public function extend($driver, Closure $callback)
  {
    $this->customCreators[$driver] = $callback;

    return $this;
  }

  protected function getConfigName()
  {
    if (empty($this->configName)) {
      $reflect = new ReflectionClass($this);
      $this->configName = 'services.' . strtolower($reflect->getShortName());
    }
    return $this->configName;
  }

  /**
   * Get the default connection name.
   *
   * @return string
   */
  public function getDefaultConnection()
  {
    return $this->app['config']["{$this->getConfigName()}.default"];
  }

  /**
   * Get all of the created "connections".
   *
   * @return array
   */
  public function getConnections()
  {
    return $this->connections;
  }

  /**
   * Set the default connection name.
   *
   * @param  string  $name
   * @return void
   */
  public function setDefaultConnection($name)
  {
    $this->app['config']["{$this->getConfigName()}.default"] = $name;
  }

  /**
   * Get an MLS connection instance.
   *
   * @param  string  $name
   * @return \App\Contracts\Drivers\Mls
   */
  public function connection($name = null)
  {
    $name = $name ?: $this->getDefaultConnection();

    // If we haven't created this connection, we'll create it based on the config
    // provided in the application.
    if (! isset($this->connections[$name])) {
      $this->connections[$name] = $this->makeConnection($name);
    }

    return $this->connections[$name];
  }

  /**
   * Get the configuration for a connection.
   *
   * @param  string  $name
   * @return array
   *
   * @throws \InvalidArgumentException
   */
  protected function configuration($name)
  {
    $name = $name ?: $this->getDefaultConnection();

    // To get the MLS connection configuration, we will just pull each of the
    // connection configurations and get the configurations for the given name.
    // If the configuration doesn't exist, we'll throw an exception and bail.
    $connections = $this->app['config']["{$this->getConfigName()}.connections"];

    $class = get_class($this);

    if (is_null($config = Arr::get($connections, $name))) {
      throw new InvalidArgumentException("{$class} [{$name}] not configured.");
    }

    if (is_null($driver = Arr::get($config, 'driver'))) {
      throw new InvalidArgumentException("{$class} [{$name}] is missing driver.");
    }

    return $config;
  }

  /**
   * Make the connection instance.
   *
   * @param  string  $name
   * @return The connection driver
   */
  protected function makeConnection($name)
  {
    $config = $this->configuration($name);

    $driver = $config['driver'];

    if (isset($this->customCreators[$driver])) {
      return $this->callCustomCreator($driver);
    } else {
      $method = 'create'.Str::studly($driver).'Driver';

      if (method_exists($this, $method)) {
        return $this->$method($name, $config);
      }
    }
    throw new InvalidArgumentException("Driver [$driver] not supported.");
  }

  /**
   * Dynamically call the default connection instance.
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public function __call($method, $parameters)
  {
    return $this->connection()->$method(...$parameters);
  }

}