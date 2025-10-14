<?php

namespace App\Resources;

class EmailHistoryResource
{
  protected $data;

  public function __construct(?array $data)
  {
    $this->data = $this->format($data);
  }

  public static function collection(array $data): array
  {
    $collection = [];
    foreach ($data as $item) {
      $collection[] = (new static($item))->get();
    }
    return $collection;
  }

  public function get(): ?array
  {
    return $this->data;
  }

  protected function format(?array $data): ?array
  {
    if (is_null($data)) {
      return null;
    }

    return [
      'id'            => $data['id'],
      'code'          => $data['code'],
      'recipient'     => $data['recipient'],
      'cc'            => $data['cc'],
      'bcc'           => $data['bcc'],
      'subject'       => $data['subject'],
      'body'          => $data['body'],
      'errorMessage'  => $data['error_message'],
      'status'        => $data['status'],
      'sentAt'        => $data['sent_at'],
      'resentTimes'   => (int) $data['resent_times'],
      'createdAt'     => $data['created_at'],
    ];
  }
}
