<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'recipient' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'cc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'bcc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'body' => [
                'type' => 'TEXT',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resent_times' => [
                'type'       => 'INT',
                'constraint' => 10,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('email_histories');
    }

    public function down() {
        $this->forge->dropTable('email_histories');
    }
}
