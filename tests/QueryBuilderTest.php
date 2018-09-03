<?php

class QueryBuilderTest extends AirtableTest
{
  public function testListRecords()
  {
    $driver = $this->getDriver();

    $records = $driver->table('Design Projects')
      ->filterByFormula("{Category} = 'Brand Identity'")
      ->get();

    $this->assertEquals(8, $records->count());

    $this->assertEquals("NYC Parks Brand Identity",
      $records->first()->Name);

    $this->assertEquals("NYC Parks Brand Identity",
      $records->first()['Name']);

    $moma = $driver->table('Design Projects')
      ->filterByFormula("{Name} = 'MOMA Brand Identity'")
      ->first();

    $this->assertEquals("MOMA Brand Identity", $moma->Name);
  }

  protected function getTestRecordName()
  {
    return sprintf("Test Created For %s", get_class($this));
  }

  public function testRecordOperations()
  {
    $driver = $this->getDriver();

    $record = $driver->make('Design Projects', [
      'Name' => $this->getTestRecordName(),
      'Due date' => now(),
      'Complete' => true,
      'Category' => 'Industrial Design',
      'Project Lead' => \Airtable\Collaborator::email('aaron@c21redwood.com'),
      'Project Photos' => [
        \Airtable\Attachment::make('https://picsum.photos/200/300'),
      ]
    ]);

    $this->assertNotNull($record->getId());

    $record->Name = $newName = 'Foo';
    $record->save();

    $stored = $driver->getRecord('Design Projects', $record->getId());

    $this->assertNotEmpty($this->getTestRecordName());

    $this->assertEquals($record->Name, $newName);

    $this->assertEquals($record->Name, $stored->Name);

    $stored->Name = $this->getTestRecordName();
    $stored->save();

    $tests = $driver->table('Design Projects')
      ->where('Name', '=', $this->getTestRecordName())
      ->get();

    $this->assertTrue($tests->count() > 0);

    $tests->each(function($test) {
      $test->delete();
    });

    $tests = $driver->table('Design Projects')
      ->where('Name', '=', $this->getTestRecordName())
      ->get();

    $this->assertTrue($tests->count() === 0);
  }

}