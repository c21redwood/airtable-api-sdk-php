<?php
namespace Airtable;

use Illuminate\Contracts\Support\Arrayable;

/**
 * An Airtable Collaborator is a user attached to a Base.
 * There are specific rules for how Collaborators must be
 * described when adding to table Records, and this class
 * implements those rules. When a Collaborator object is
 * added to a Record, it is automatically serialized.
 */
class Collaborator implements Arrayable {

  protected $id;

  protected $email;

  static function email($email)
  {
    return new self(null, $email);
  }

  static function id($id)
  {
    return new self($id, null);
  }

  private function __construct($id = null, $email = null) {
    if (!$id && !$email) {
      throw new Exception("One of [id] or [email] is required");
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("{$email} is not a valid email address");
    }

    $this->id = $id;
    $this->email = $email;
  }

  public function toArray()
  {
    return array_filter([
      'id' => $this->id,
      'email' => $this->email,
    ]);
  }
}