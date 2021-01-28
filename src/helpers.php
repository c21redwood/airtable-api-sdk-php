<?php
use Illuminate\Support\Arr;

if (!function_exists('airtable_connect')) {

  /**
   * Create a new Airtable connection without the benefit of the Connection Manager
   * @param string $baseId
   * @param string $apiKey
   * @return \Airtable\Contracts\Driver
   * @throws Exception
   */
  function airtable_connect($baseId = null, $apiKey = null, $driver = \Airtable\Drivers\Guzzle::class) {
    static $connections;

    if (is_null($connections)) {
      $connections = [];
    }

    if (!empty($baseId)) {
      if (empty($connections[$baseId])) {
        if (empty($apiKey)) {
          throw new \Exception("Cannot create Airtable connection without API key (argument #2)");
        }

        $connections[$baseId] = ( new $driver($baseId) )
          ->setBaseId($baseId)
          ->setApiKey($apiKey);
      }

      return $connections[$baseId];
    } else {
      if (empty($connections)) {
        throw new \Exception("There are no Airtable connections");
      }
      return Arr::first($connections);
    }
  }

  /**
   * Alias for airtable_connect
   * @see airtable_connect($baseId, $apiKey, $driver)
   * @return \Airtable\Contracts\Driver
   */
  function airtable(/* ... */)
  {
    return call_user_func_array('airtable_connect', func_get_args());
  }

}