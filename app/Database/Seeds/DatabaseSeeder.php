<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder {
  public function run() {
    // Call the seeder for user roles first
    $this->call('UserRoleSeeder');
  }
}
