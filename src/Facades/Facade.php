<?php
namespace Airtable\Facades;

use Airtable\Managers\Airtable;
use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade {

  protected static function getFacadeAccessor()
  {
    return Airtable::class;
  }

}