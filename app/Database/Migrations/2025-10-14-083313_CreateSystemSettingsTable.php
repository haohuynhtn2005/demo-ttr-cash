<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'meta_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'meta_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'field_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('system_settings');
    }

    public function down()
    {
        $this->forge->dropTable('system_settings');
    }
}