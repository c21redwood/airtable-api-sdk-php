<?php
namespace Airtable\Contracts;

use Airtable\AirtableException;
use Airtable\QueryBuilder;
use Airtable\Record;
use Illuminate\Support\Collection;

interface Driver {

  function setBaseId($baseId);

  /**
   * @param $tableName
   * @return QueryBuilder
   */
  function table($tableName);

  /**
   * @param QueryBuilder $builder
   * @throws AirtableException On connection errors
   * @return Collection
   */
  function listRecords(QueryBuilder $builder);

  function getRecord($tableName, $id);

  function make($tableName, array $array = []);

  function create(Record $record);

  function newRecord($tableName, array $array = []);

  function update(Record $record);

  function delete($tableName, $id);

  function setApiKey($apiKey);

}