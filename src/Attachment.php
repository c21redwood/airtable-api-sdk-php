<?php
namespace Airtable;

use Illuminate\Contracts\Support\Arrayable;

/**
 * An Airtable Attachment is a file attached to a Base.
 * There are specific rules for how Collaborators must be
 * described when adding to table Records, and this class
 * implements those rules. When a Collaborator object is
 * added to a Record, it is automatically serialized.
 */
class Attachment implements Arrayable {

  protected $url;

  protected $filename;

  static function make($url, $filename = null)
  {
    return new Attachment($url, $filename);
  }

  private function __construct($url, $filename = null) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException("Not a valid URL: {$url}");
    }

    $this->url = $url;
    $this->filename = $filename;
  }

  public function toArray()
  {
    return array_filter([
      'url' => $this->url,
      'filename' => $this->filename,
    ]);
  }
}