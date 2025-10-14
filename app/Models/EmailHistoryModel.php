<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailHistoryModel extends Model
{
    protected $table            = 'email_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'code', 'recipient', 'cc', 'bcc', 'subject', 'body',
        'error_message', 'status', 'sent_at', 'resent_times'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}