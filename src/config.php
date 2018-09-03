<?php

return [

  'default' => env('AIRTABLE_DEFAULT_BASE') ?: 'My Base',

  'connections' => [

    'My Base' => [
      'driver' => 'guzzle',
      'base_id' => env('AIRTABLE_BASE_ID'),
      'api_key' => env('AIRTABLE_API_KEY'),
    ],

  ]

];