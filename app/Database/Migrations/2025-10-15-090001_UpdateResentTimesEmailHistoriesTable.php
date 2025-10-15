<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateResentTimesConstraintInEmailHistoriesTable extends Migration {
  public function up() {
    $fields = [
      'resent_times' => [
        'type'       => 'INT',
        'constraint' => 2,
        'default'    => 0,
      ],
    ];
    $this->forge->modifyColumn('email_histories', $fields);
  }

  public function down() {
    $fields = [
      'resent_times' => [
        'type'       => 'INT',
        'constraint' => 10,
        'default'    => 0,
      ],
    ];
    $this->forge->modifyColumn('email_histories', $fields);
  }
}
