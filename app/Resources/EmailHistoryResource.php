<?php

namespace App\Resources;

class EmailHistoryResource
{
    protected $data;

    public function __construct($resource)
    {
        $this->data = $resource;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function get(): array
    {
        if (!$this->data) {
            return [];
        }

        return [
            'id'            => (int) $this->data['id'],
            'code'          => $this->data['code'],
            'recipient'     => $this->data['recipient'],
            'cc'            => $this->data['cc'],
            'bcc'           => $this->data['bcc'],
            'subject'       => $this->data['subject'],
            'body'          => $this->data['body'],
            'status'        => (int) $this->data['status'],
            'status_text'   => $this->data['status'] == 1 ? 'Sent' : 'Failed/Pending',
            'error_message' => $this->data['error_message'],
            'sent_at'       => $this->data['sent_at'],
            'resent_times'  => (int) $this->data['resent_times'],
            'created_at'    => $this->data['created_at'],
        ];
    }

    /**
     * Transform a collection of resources into an array.
     *
     * @param array $resources
     * @return array
     */
    public static function collection(array $resources): array
    {
        return array_map(fn ($resource) => (new static($resource))->get(), $resources);
    }
}