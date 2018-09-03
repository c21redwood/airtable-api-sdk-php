<?php
namespace Airtable\Drivers;

use Airtable\AirtableException;
use Airtable\Contracts\Driver;
use Airtable\QueryBuilder;
use Airtable\Record;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;

class Guzzle extends AbstractDriver implements Driver {

  protected $httpClient;

  function setHttpClient(HttpClient $httpClient)
  {
    $this->httpClient = $httpClient;
  }

  /**
   * @return HttpClient
   */
  function getHttpClient()
  {
    if (empty($this->httpClient)) {
      $this->httpClient = new HttpClient;
    }

    return $this->httpClient;
  }

  /**
   * @param $operation
   * @param Response $response
   * @param GuzzleException $e
   * @throws AirtableException
   */
  protected function handleClientException($operation, GuzzleException $e)
  {
    $response = $e->getResponse();

    $body = (string) $response->getBody();

    $result = json_decode($body);

    switch ($e->getCode()) {
      case 422:
        throw new AirtableException("{$operation} failed: {$result->error->message}", $e->getCode(), $e);
      default:
        throw new AirtableException("{$operation} failed: " . $e->getMessage(), $e->getCode(), $e);
    }

  }

  /**
   * @param QueryBuilder $builder
   * @return Collection
   * @throws AirtableException
   */
  function listRecords(QueryBuilder $builder)
  {
    $url = $this->getTableUrl($builder->getTableName(), $builder->toArray());

    try {
      $response = $this->getHttpClient()->request('GET', $url, [
        'headers' => $this->getHeaders(),
      ]);
    } catch (GuzzleException $e) {
      $this->handleClientException('listRecord', $e);
    }

    $body = (string) $response->getBody();

    $listRecords = json_decode($body);

    return (new Collection($listRecords->records ?: []))
      ->map(function($data) use ($builder) {
        return (new Record)
          ->setDriver($this)
          ->setTableName($builder->getTableName())
          ->setId($data->id)
          ->fill((array) $data->fields);
      });
  }

  function make($tableName, array $array = [])
  {
    $record = $this->newRecord($tableName, $array);
    $this->create($record);
    return $record;
  }

  /**
   * @param $tableName
   * @param $id
   * @return Record
   * @throws AirtableException
   */
  function getRecord($tableName, $id)
  {
    $url = $this->getRecordUrl($tableName, $id);

    try {
      $response = $this->getHttpClient()->request('GET', $url, [
        'headers' => $this->getHeaders(),
      ]);
    } catch (GuzzleException $e) {
      $this->handleClientException('getRecord', $e);
    }

    $body = (string) $response->getBody();

    $getRecord = json_decode($body);

    return (new Record)
      ->setDriver($this)
      ->setTableName($tableName)
      ->setId($getRecord->id)
      ->fill((array) $getRecord->fields);
  }

  /**
   * @param Record $record
   * @return string The new ID
   * @throws AirtableException
   */
  function create(Record $record)
  {
    $url = $this->getTableUrl($record->getTableName());

//    dd($record->toJson());

    try {
      $response = $this->getHttpClient()->request('POST', $url, [
        'headers' => $this->getHeaders(),
        'body' => $record->toJson(),
      ]);
    } catch (GuzzleException $e) {
      $this->handleClientException('create', $e);
    }

    $body = (string) $response->getBody();

    $create = json_decode($body);

    $record
      ->setId($create->id)
      ->fill((array) $create->fields);

    return $record->getId();
  }

  /**
   * @param Record $record
   * @throws AirtableException
   */
  function update(Record $record)
  {
    $url = $this->getRecordUrl($record->getTableName(), $record->getId());

    try {
      $response = $this->getHttpClient()->request('PUT', $url, [
        'headers' => $this->getHeaders(),
        'body' => $record->toJson(),
      ]);
    } catch (GuzzleException $e) {
      $this->handleClientException('update', $e);
    }

    $body = (string) $response->getBody();

    $update = json_decode($body);

    $record->fill((array) $update->fields);
  }

  /**
   * @param $tableName
   * @param $id
   */
  function delete($tableName, $id)
  {
    $url = $this->getRecordUrl($tableName, $id);

    try {
      $response = $this->getHttpClient()->request('DELETE', $url, [
        'headers' => $this->getHeaders(),
      ]);
    } catch (GuzzleException $e) {
      $this->handleClientException('delete', $e);
    }
  }

}