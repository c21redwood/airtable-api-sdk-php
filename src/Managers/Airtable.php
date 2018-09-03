<?php
namespace Airtable\Managers;

use Airtable\Contracts\Driver;
use Airtable\Drivers\Guzzle;
use Airtable\Drivers\WordPress;

class Airtable extends AbstractManager {

  function getConfigName()
  {
    return 'airtable';
  }

  function createGuzzleDriver($name, $config)
  {
    return $this->configureDriver(new Guzzle($name), $config);
  }

  protected function configureDriver(Driver $driver, $config)
  {
    if (!empty($config['api_key'])) {
      $driver->setApiKey($config['api_key']);
    }

    if (!empty($config['base_id'])) {
      $driver->setBaseId($config['base_id']);
    }

    return $driver;
  }

  /**
   * @param $name
   * @return array|false|mixed|string|void
   */
  function getWordPressConfigOption($name)
  {
    $option = 'airtable_' . strtolower($name);

    if ($value = get_option($option)) {
      return $value;
    }

    $env = 'AIRTABLE_' . strtoupper($name);

    if (defined($env)) {
      return constant($env);
    }

    if (function_exists('getenv')) {
      return getenv($env);
    }

    if (function_exists('env')) {
      return env($env);
    }
  }

}