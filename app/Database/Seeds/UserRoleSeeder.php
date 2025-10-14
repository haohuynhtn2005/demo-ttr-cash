<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserRoleSeeder extends Seeder {
    public function run() {
        $data = [
            [
                'name'        => 'Administrator',
                'key'         => 'ADMIN',
                'description' => 'Super user with all permissions',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Staff',
                'key'         => 'STAFF',
                'description' => 'Staff user with limited permissions',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Standard User',
                'key'         => 'USER',
                'description' => 'Regular user with limited permissions',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Using the Query Builder to insert data
        $this->db->table('user_roles')->insertBatch($data);
    }
}
