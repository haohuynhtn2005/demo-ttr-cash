<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder {
  public function run() {
    $data = [
      [
        'name'       => 'Admin User',
        'email'      => 'admin@example.com',
        'password'   => password_hash(123456, PASSWORD_DEFAULT),
        'role_id'    => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ],
      [
        'name'       => 'Manager User',
        'email'      => 'manager@example.com',
        'password'   => password_hash(123456, PASSWORD_DEFAULT),
        'role_id'    => 2,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ],
      [
        'name'       => 'Normal User',
        'email'      => 'user@example.com',
        'password'   => password_hash(123456, PASSWORD_DEFAULT),
        'role_id'    => 3,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ],
    ];

    // Using Query Builder
    $this->db->table('users')->insertBatch($data);
  }
}
