<?php
namespace Airtable;

use Airtable\Contracts\Driver;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @see Driver::table(string $tableName)
 * @package Airtable
 */
class QueryBuilder implements Arrayable {

  /**
   * @var array
   */
  protected $fields = [];

  /**
   * @var string
   */
  protected $filterByFormula = null;

  /**
   * @var int
   */
  protected $maxRecords = null;

  /**
   * @var int
   */
  protected $pageSize = 100;

  /**
   * @var array
   */
  protected $sort = [];

  /**
   * @var string
   */
  protected $view = null;

  /**
   * @var string
   */
  protected $cellFormat = 'json';

  /**
   * @var null
   */
  protected $timeZone = null;

  /**
   * @var null
   */
  protected $userLocale = null;

  /**
   * @var string
   */
  protected $offset = null;

  /**
   * @var string
   */
  protected $tableName = null;

  /**
   * @var array
   */
  static protected $validTimeZones = [
    'Africa/Abidjan',
    'Africa/Accra',
    'Africa/Algiers',
    'Africa/Bissau',
    'Africa/Cairo',
    'Africa/Casablanca',
    'Africa/Ceuta',
    'Africa/El_Aaiun',
    'Africa/Johannesburg',
    'Africa/Khartoum',
    'Africa/Lagos',
    'Africa/Maputo',
    'Africa/Monrovia',
    'Africa/Nairobi',
    'Africa/Ndjamena',
    'Africa/Tripoli',
    'Africa/Tunis',
    'Africa/Windhoek',
    'America/Adak',
    'America/Anchorage',
    'America/Araguaina',
    'America/Argentina/Buenos_Aires',
    'America/Argentina/Catamarca',
    'America/Argentina/Cordoba',
    'America/Argentina/Jujuy',
    'America/Argentina/La_Rioja',
    'America/Argentina/Mendoza',
    'America/Argentina/Rio_Gallegos',
    'America/Argentina/Salta',
    'America/Argentina/San_Juan',
    'America/Argentina/San_Luis',
    'America/Argentina/Tucuman',
    'America/Argentina/Ushuaia',
    'America/Asuncion',
    'America/Atikokan',
    'America/Bahia',
    'America/Bahia_Banderas',
    'America/Barbados',
    'America/Belem',
    'America/Belize',
    'America/Blanc-Sablon',
    'America/Boa_Vista',
    'America/Bogota',
    'America/Boise',
    'America/Cambridge_Bay',
    'America/Campo_Grande',
    'America/Cancun',
    'America/Caracas',
    'America/Cayenne',
    'America/Chicago',
    'America/Chihuahua',
    'America/Costa_Rica',
    'America/Creston',
    'America/Cuiaba',
    'America/Curacao',
    'America/Danmarkshavn',
    'America/Dawson',
    'America/Dawson_Creek',
    'America/Denver',
    'America/Detroit',
    'America/Edmonton',
    'America/Eirunepe',
    'America/El_Salvador',
    'America/Fort_Nelson',
    'America/Fortaleza',
    'America/Glace_Bay',
    'America/Godthab',
    'America/Goose_Bay',
    'America/Grand_Turk',
    'America/Guatemala',
    'America/Guayaquil',
    'America/Guyana',
    'America/Halifax',
    'America/Havana',
    'America/Hermosillo',
    'America/Indiana/Indianapolis',
    'America/Indiana/Knox',
    'America/Indiana/Marengo',
    'America/Indiana/Petersburg',
    'America/Indiana/Tell_City',
    'America/Indiana/Vevay',
    'America/Indiana/Vincennes',
    'America/Indiana/Winamac',
    'America/Inuvik',
    'America/Iqaluit',
    'America/Jamaica',
    'America/Juneau',
    'America/Kentucky/Louisville',
    'America/Kentucky/Monticello',
    'America/La_Paz',
    'America/Lima',
    'America/Los_Angeles',
    'America/Maceio',
    'America/Managua',
    'America/Manaus',
    'America/Martinique',
    'America/Matamoros',
    'America/Mazatlan',
    'America/Menominee',
    'America/Merida',
    'America/Metlakatla',
    'America/Mexico_City',
    'America/Miquelon',
    'America/Moncton',
    'America/Monterrey',
    'America/Montevideo',
    'America/Nassau',
    'America/New_York',
    'America/Nipigon',
    'America/Nome',
    'America/Noronha',
    'America/North_Dakota/Beulah',
    'America/North_Dakota/Center',
    'America/North_Dakota/New_Salem',
    'America/Ojinaga',
    'America/Panama',
    'America/Pangnirtung',
    'America/Paramaribo',
    'America/Phoenix',
    'America/Port-au-Prince',
    'America/Port_of_Spain',
    'America/Porto_Velho',
    'America/Puerto_Rico',
    'America/Rainy_River',
    'America/Rankin_Inlet',
    'America/Recife',
    'America/Regina',
    'America/Resolute',
    'America/Rio_Branco',
    'America/Santarem',
    'America/Santiago',
    'America/Santo_Domingo',
    'America/Sao_Paulo',
    'America/Scoresbysund',
    'America/Sitka',
    'America/St_Johns',
    'America/Swift_Current',
    'America/Tegucigalpa',
    'America/Thule',
    'America/Thunder_Bay',
    'America/Tijuana',
    'America/Toronto',
    'America/Vancouver',
    'America/Whitehorse',
    'America/Winnipeg',
    'America/Yakutat',
    'America/Yellowknife',
    'Antarctica/Casey',
    'Antarctica/Davis',
    'Antarctica/DumontDUrville',
    'Antarctica/Macquarie',
    'Antarctica/Mawson',
    'Antarctica/Palmer',
    'Antarctica/Rothera',
    'Antarctica/Syowa',
    'Antarctica/Troll',
    'Antarctica/Vostok',
    'Asia/Almaty',
    'Asia/Amman',
    'Asia/Anadyr',
    'Asia/Aqtau',
    'Asia/Aqtobe',
    'Asia/Ashgabat',
    'Asia/Baghdad',
    'Asia/Baku',
    'Asia/Bangkok',
    'Asia/Barnaul',
    'Asia/Beirut',
    'Asia/Bishkek',
    'Asia/Brunei',
    'Asia/Chita',
    'Asia/Choibalsan',
    'Asia/Colombo',
    'Asia/Damascus',
    'Asia/Dhaka',
    'Asia/Dili',
    'Asia/Dubai',
    'Asia/Dushanbe',
    'Asia/Gaza',
    'Asia/Hebron',
    'Asia/Ho_Chi_Minh',
    'Asia/Hong_Kong',
    'Asia/Hovd',
    'Asia/Irkutsk',
    'Asia/Jakarta',
    'Asia/Jayapura',
    'Asia/Jerusalem',
    'Asia/Kabul',
    'Asia/Kamchatka',
    'Asia/Karachi',
    'Asia/Kathmandu',
    'Asia/Khandyga',
    'Asia/Kolkata',
    'Asia/Krasnoyarsk',
    'Asia/Kuala_Lumpur',
    'Asia/Kuching',
    'Asia/Macau',
    'Asia/Magadan',
    'Asia/Makassar',
    'Asia/Manila',
    'Asia/Nicosia',
    'Asia/Novokuznetsk',
    'Asia/Novosibirsk',
    'Asia/Omsk',
    'Asia/Oral',
    'Asia/Pontianak',
    'Asia/Pyongyang',
    'Asia/Qatar',
    'Asia/Qyzylorda',
    'Asia/Rangoon',
    'Asia/Riyadh',
    'Asia/Sakhalin',
    'Asia/Samarkand',
    'Asia/Seoul',
    'Asia/Shanghai',
    'Asia/Singapore',
    'Asia/Srednekolymsk',
    'Asia/Taipei',
    'Asia/Tashkent',
    'Asia/Tbilisi',
    'Asia/Tehran',
    'Asia/Thimphu',
    'Asia/Tokyo',
    'Asia/Tomsk',
    'Asia/Ulaanbaatar',
    'Asia/Urumqi',
    'Asia/Ust-Nera',
    'Asia/Vladivostok',
    'Asia/Yakutsk',
    'Asia/Yekaterinburg',
    'Asia/Yerevan',
    'Atlantic/Azores',
    'Atlantic/Bermuda',
    'Atlantic/Canary',
    'Atlantic/Cape_Verde',
    'Atlantic/Faroe',
    'Atlantic/Madeira',
    'Atlantic/Reykjavik',
    'Atlantic/South_Georgia',
    'Atlantic/Stanley',
    'Australia/Adelaide',
    'Australia/Brisbane',
    'Australia/Broken_Hill',
    'Australia/Currie',
    'Australia/Darwin',
    'Australia/Eucla',
    'Australia/Hobart',
    'Australia/Lindeman',
    'Australia/Lord_Howe',
    'Australia/Melbourne',
    'Australia/Perth',
    'Australia/Sydney',
    'Europe/Amsterdam',
    'Europe/Andorra',
    'Europe/Astrakhan',
    'Europe/Athens',
    'Europe/Belgrade',
    'Europe/Berlin',
    'Europe/Brussels',
    'Europe/Bucharest',
    'Europe/Budapest',
    'Europe/Chisinau',
    'Europe/Copenhagen',
    'Europe/Dublin',
    'Europe/Gibraltar',
    'Europe/Helsinki',
    'Europe/Istanbul',
    'Europe/Kaliningrad',
    'Europe/Kiev',
    'Europe/Kirov',
    'Europe/Lisbon',
    'Europe/London',
    'Europe/Luxembourg',
    'Europe/Madrid',
    'Europe/Malta',
    'Europe/Minsk',
    'Europe/Monaco',
    'Europe/Moscow',
    'Europe/Oslo',
    'Europe/Paris',
    'Europe/Prague',
    'Europe/Riga',
    'Europe/Rome',
    'Europe/Samara',
    'Europe/Simferopol',
    'Europe/Sofia',
    'Europe/Stockholm',
    'Europe/Tallinn',
    'Europe/Tirane',
    'Europe/Ulyanovsk',
    'Europe/Uzhgorod',
    'Europe/Vienna',
    'Europe/Vilnius',
    'Europe/Volgograd',
    'Europe/Warsaw',
    'Europe/Zaporozhye',
    'Europe/Zurich',
    'Indian/Chagos',
    'Indian/Christmas',
    'Indian/Cocos',
    'Indian/Kerguelen',
    'Indian/Mahe',
    'Indian/Maldives',
    'Indian/Mauritius',
    'Indian/Reunion',
    'Pacific/Apia',
    'Pacific/Auckland',
    'Pacific/Bougainville',
    'Pacific/Chatham',
    'Pacific/Chuuk',
    'Pacific/Easter',
    'Pacific/Efate',
    'Pacific/Enderbury',
    'Pacific/Fakaofo',
    'Pacific/Fiji',
    'Pacific/Funafuti',
    'Pacific/Galapagos',
    'Pacific/Gambier',
    'Pacific/Guadalcanal',
    'Pacific/Guam',
    'Pacific/Honolulu',
    'Pacific/Kiritimati',
    'Pacific/Kosrae',
    'Pacific/Kwajalein',
    'Pacific/Majuro',
    'Pacific/Marquesas',
    'Pacific/Nauru',
    'Pacific/Niue',
    'Pacific/Norfolk',
    'Pacific/Noumea',
    'Pacific/Pago_Pago',
    'Pacific/Palau',
    'Pacific/Pitcairn',
    'Pacific/Pohnpei',
    'Pacific/Port_Moresby',
    'Pacific/Rarotonga',
    'Pacific/Tahiti',
    'Pacific/Tarawa',
    'Pacific/Tongatapu',
    'Pacific/Wake',
    'Pacific/Wallis'
  ];

  /**
   * @var \Airtable\Contracts\Driver
   */
  protected $driver;

  function __construct(Driver $driver, $tableName)
  {
    $this->driver = $driver;

    $this->tableName = $tableName;
  }

  function getTableName()
  {
    return $this->tableName;
  }

  function addField($field)
  {
    if (is_array($field)) {
      return $this->addFields($field);
    }

    if (!in_array($field, $this->fields)) {
      $this->fields[] = $field;
    }

    return $this;
  }

  function addFields(array $fields = [])
  {
    $this->fields = array_unique(array_merge($this->fields, $fields));

    return $this;
  }

  function select(array $fields = [])
  {
    return $this->addFields($fields);
  }

  /**
   * @param array|string $fields One or more fields to add to the query
   * @return $this
   */
  function fields($fields)
  {
    return $this->addField($fields);
  }

  /**
   * Specify a formula to filter the result set by; when combined with a specific view, only records in that view which
   * satisfy the formula will be returned
   * @param string $string
   * @return $this
   * @see https://support.airtable.com/hc/en-us/articles/203255215-Formula-Field-Reference
   */
  function filterByFormula(string $string)
  {
    $this->filterByFormula = $string;

    return $this;
  }

  /**
   * @param $field
   * @param string $operator
   * @param $value
   * @return QueryBuilder
   */
  function where($field, $operator = '=', $value)
  {
    return $this->filterByFormula(sprintf("{{$field}} {$operator} '%s'", str_replace("'", "\\'", $value)));
  }

  /**
   * The maximum total number of records that will be returned in your requests. If this value is larger than pageSize
   * (which is 100 by default), you may have to load multiple pages to reach this total.
   * @param int $maxRecords
   * @return $this
   */
  function maxRecords(int $maxRecords)
  {
    $this->maxRecords = $maxRecords;

    return $this;
  }

  /**
   * The number of records returned in each request. Must be less than or equal to 100. Default is 100.
   * @param int $pageSize
   * @return $this
   */
  function pageSize(int $pageSize)
  {
    $this->pageSize = $pageSize;

    return $this;
  }

  /**
   * Find and return the Record in this table with the given ID.
   * @param $id
   * @return Record
   */
  function find($id)
  {
    return $this->driver->getRecord($this->getTableName(), $id);
  }

  /**
   * Add a sorting criteria to the result set
   * @param $field The name of the field
   * @param string $direction The sort direction; must be one of "asc" or "desc"; the default is "asc"
   * @return $this
   */
  function sortBy($field, $direction = 'asc')
  {
    $direction = strtolower($direction);

    if (!in_array($direction, ['asc', 'desc'])) {
      throw new \InvalidArgumentException('Sort direction must be one of "asc" or "desc"');
    }

    $this->sort[] = (object) [
      'field' => $field,
      'direction' => $direction,
    ];

    return $this;
  }

  /**
   * @param $field
   * @param string $direction
   * @return QueryBuilder
   * @see QueryBuilder::sortBy
   */
  function sort($field, $direction = 'asc')
  {
    return $this->sortBy($field, $direction);
  }

  /**
   * The name or ID of a view in the Guides table. If set, only the records in that view will be returned. The records
   * will be sorted according to the order of the view.
   * @param string $id
   * @return $this
   */
  function view(string $id)
  {
    $this->view = $id;

    return $this;
  }

  /**
   * The format that should be used for cell values. Supported values are: "json": cells will be formatted as JSON,
   * depending on the field type; "string": cells will be formatted as user-facing strings, regardless of the field
   * type. Note: You should not rely on the format of these strings, as it is subject to change.
   * @param string $cellFormat
   * @return $this
   */
  function cellFormat(string $cellFormat)
  {
    if (!in_array($cellFormat, ['json', 'string'])) {
      throw new \InvalidArgumentException('Invalid value for cellFormat: must be one of "json" or "string"');
    }

    $this->cellFOrmat = $cellFormat;

    return $this;
  }

  /**
   * The time zone that should be used to format dates when using "string" as the cellFormat.
   * This parameter is required when using "string" as the cellFormat.
   * @param string $timeZone
   * @return $this
   * @see https://support.airtable.com/hc/en-us/articles/216141558-Supported-timezones-for-SET-TIMEZONE
   */
  function timeZone(string $timeZone)
  {
    if (!in_array($timeZone, self::$validTimeZones)) {
      throw new \InvalidArgumentException("Invalid value for timeZone; see https://support.airtable.com/hc/en-us/articles/216141558-Supported-timezones-for-SET-TIMEZONE");
    }

    $this->timeZone = $timeZone;

    return $this;
  }

  /**
   * The user locale that should be used to format dates when using "string" as the cellFormat.
   * This parameter is required when using "string" as the cellFormat.
   * @param string $userLocale
   * @return $this
   * @see https://support.airtable.com/hc/en-us/articles/220340268-Supported-locale-modifiers-for-SET-LOCALE
   */
  function userLocale(string $userLocale)
  {
    $this->userLocale = $userLocale;

    return $this;
  }

  /**
   * Set the page offset for the result set
   * @param string $offset
   * @return $this
   */
  function offset(string $offset)
  {
    $this->offset = $offset;

    return $this;
  }

  function get()
  {
    return $this->driver->listRecords($this);
  }

  function first()
  {
    return $this->get()->first();
  }

  function paginate()
  {

  }

  /**
   * Build the query parameters
   * @return array
   */
  function toArray()
  {
    if ($this->cellFormat === 'string') {

      if (empty($this->timeZone)) {
        throw new \InvalidArgumentException("When using cellFormat = 'string', you must also specify a valid timeZone for conversion of dates to strings; see QueryBuilder::timeZone");
      }

      if (empty($this->userLocale)) {
        throw new \InvalidArgumentException("When using cellFormat = 'string', you must also specify a valid userLocale for conversion of dates to strings; see QueryBuilder::userLocale");
      }

    }

    $query = [
      'fields' => $this->fields,
      'filterByFormula' => $this->filterByFormula,
      'maxRecords' => $this->maxRecords,
      'pageSize' => $this->pageSize,
      'sort' => $this->sort,
      'view' => $this->view,
      'cellFormat' => $this->cellFormat,
      'timeZone' => $this->timeZone,
      'userLocale' => $this->userLocale,
    ];

    return array_filter($query);
  }

  function toJson()
  {
    return json_encode($this->toQuery(), JSON_PRETTY_PRINT);
  }

}