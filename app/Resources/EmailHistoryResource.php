<?php

namespace App\Resources;

use JsonSerializable;

class EmailHistoryResource  implements JsonSerializable {
    protected $resource;

    public function __construct($resource) {
        $this->resource = $resource;
    }


    public function jsonSerialize(): mixed {
        return $this->toArray();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'id'            => (int) $this->resource['id'],
            'code'          => $this->resource['code'],
            'recipient'     => $this->resource['recipient'],
            'cc'            => $this->resource['cc'],
            'bcc'           => $this->resource['bcc'],
            'subject'       => $this->resource['subject'],
            'body'          => $this->resource['body'],
            'status'        => (int) $this->resource['status'],
            'status_text'   => $this->resource['status'] == 1 ? 'Sent' : 'Failed/Pending',
            'error_message' => $this->resource['error_message'],
            'sent_at'       => $this->resource['sent_at'],
            'resent_times'  => (int) $this->resource['resent_times'],
            'created_at'    => $this->resource['created_at'],
        ];
    }

    /**
     * Transform a collection of resources into an array.
     *
     * @param array $resources
     * @return array
     */
    public static function collection(array $resources): array {
        return array_map(fn($resource) => (new static($resource))->get(), $resources);
    }
}
