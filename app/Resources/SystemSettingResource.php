<?php

namespace App\Resources;

use JsonSerializable;

class SystemSettingResource implements JsonSerializable
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->resource['id'],
            'meta_key'          => $this->resource['meta_key'],
            'meta_value'        => $this->resource['meta_value'],
            'label'             => $this->resource['label'],
            'field_type'        => $this->resource['field_type'],
        ];
    }

    public static function collection(array $items): array
    {
        return array_map(fn($item) => (new static($item))->toArray(), $items);
    }
}

