<?php
use PHPUnit\Framework\TestCase;

use Airtable\Drivers\Guzzle as AirtableDriver;
use Airtable\Managers\Airtable;

abstract class AirtableTest extends TestCase {

  /**
   * @var Airtable
   */
  protected $manager;

  /**
   * @var \Airtable\Contracts\Driver
   */
  protected $driver;

  /**
   * @return \Airtable\Contracts\Driver
   */
  protected function getDriver()
  {
    if (empty($this->driver)) {
      $this->driver = (new AirtableDriver('Test'))
        ->setApiKey(getenv('AIRTABLE_API_KEY'))
        ->setBaseId(getenv('AIRTABLE_BASE_ID'));
    }

    return $this->driver;
  }

  /**
   * @return Airtable
   */
  protected function getManager()
  {
    if (empty($this->manager)) {
      $this->manager = new Airtable;
    }

    return $this->manager;
  }

}