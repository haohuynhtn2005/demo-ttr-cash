<?php

namespace App\Requests\SystemSetting;

class CreateSystemSettingRequest
{
    public static function rules(): array
    {
        return [
            'meta_key'   => 'required|max_length[255]', //varchar
            'meta_value' => 'permit_empty', //text
            'label'      => 'required|max_length[255]', //varchar
            'field_type' => 'permit_empty|max_length[255]',
        ];
    }

    public static function messages(): array
    {
        return [
            'meta_key.required'   => 'Meta key is required.',
            'meta_key.max_length' => 'Meta key cannot exceed 255 characters.',

            'label.required'      => 'Label is required.',
            'label.max_length'    => 'Label cannot exceed 255 characters.',

            'field_type.max_length' => 'Field type cannot exceed 255 characters.',
        ];
    }
}
