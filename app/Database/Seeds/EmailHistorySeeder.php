<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class EmailHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'code'          => 'user_registration',
                'recipient'     => 'testuser1@example.com',
                'cc'            => null,
                'bcc'           => null,
                'subject'       => 'Welcome to Our Service!',
                'body'          => '<h1>Welcome!</h1><p>Thank you for registering.</p>',
                'error_message' => null,
                'status'        => 1, // 1 = Sent
                'sent_at'       => Time::now(),
                'resent_times'  => 0,
                'created_at'    => Time::now(),
                'updated_at'    => Time::now(),
                'deleted_at'    => null,
            ],
            [
                'code'          => 'password_reset',
                'recipient'     => 'testuser2@example.com',
                'cc'            => null,
                'bcc'           => null,
                'subject'       => 'Password Reset Request',
                'body'          => '<h1>Password Reset</h1><p>You requested a password reset.</p>',
                'error_message' => 'SMTP Error: Could not connect to host.',
                'status'        => 0, // 0 = Failed/Pending
                'sent_at'       => null,
                'resent_times'  => 1,
                'created_at'    => Time::now()->subHours(1),
                'updated_at'    => Time::now(),
                'deleted_at'    => null,
            ],
        ];

        // Using Query Builder
        $this->db->table('email_histories')->insertBatch($data);
    }
}