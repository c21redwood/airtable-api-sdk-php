<?php
namespace Airtable\Drivers;

use Airtable\Contracts\Driver;
use Airtable\QueryBuilder;
use Airtable\Record;

abstract class AbstractDriver implements Driver {

  protected $apiKey;

  protected $apiDomain = 'api.airtable.com';

  protected $apiVersion = 0;

  protected $baseId;

  protected $name;

  function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * @param $baseId
   * @return $this
   */
  function setBaseId($baseId)
  {
    $this->baseId = $baseId;

    return $this;
  }

  /**
   * @param string $tableName
   * @param array $array
   * @return Record
   */
  function newRecord($tableName, array $array = [])
  {
    return (new Record)
      ->setDriver($this)
      ->setTableName($tableName)
      ->fill($array);
  }


  /**
   * Create a new QueryBuilder
   * @param $tableName
   */
  function table($tableName)
  {
    return new QueryBuilder($this, $tableName);
  }

  /**
   * Set the API key on this client
   * @param $apiKey
   * @return $this
   */
  function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;

    return $this;
  }

  /**
   * @param string $tableId
   * @param array $parameters
   * @return string
   */
  protected function getTableUrl($tableName, array $parameters = [])
  {
    $url = "https://{$this->apiDomain}/v{$this->apiVersion}/{$this->baseId}/" . rawurlencode($tableName);

    if ($parameters) {
      $url .= '?' . http_build_query($parameters);
    }

    return $url;
  }

  /**
   * @param $tableName
   * @param $recordId
   * @return string
   */
  protected function getRecordUrl($tableName, $recordId)
  {
    return "https://{$this->apiDomain}/v{$this->apiVersion}/{$this->baseId}/" . rawurlencode($tableName) . "/" . rawurlencode($recordId);
  }

  protected function getHeaders()
  {
    return [
      'Authorization' => "Bearer {$this->apiKey}",
      'Accepts' => 'application/json',
      'Content-type' => 'application/json',
    ];
  }

}