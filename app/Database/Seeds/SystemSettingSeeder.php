<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SystemSettingSeeder extends Seeder {
  public function run() {
    $now = Time::now();

    $data = [
      [
        'meta_key'   => 'site_name',
        'meta_value' => 'TTR Cash',
        'label'      => 'Site Name',
        'field_type' => 'text',
        'options'    => null,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'meta_key'   => 'site_description',
        'meta_value' => 'A demo application for TTR.',
        'label'      => 'Site Description',
        'field_type' => 'textarea',
        'options'    => null,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'meta_key'   => 'items_per_page',
        'meta_value' => '15',
        'label'      => 'Items Per Page',
        'field_type' => 'number',
        'options'    => null,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'meta_key'   => 'maintenance_mode',
        'meta_value' => '0',
        'label'      => 'Maintenance Mode',
        'field_type' => 'select',
        'options'    => json_encode(['1' => 'On', '0' => 'Off']),
        'created_at' => $now,
        'updated_at' => $now,
      ],
    ];

    // Using Query Builder
    $this->db->table('system_settings')->insertBatch($data);
  }
}
