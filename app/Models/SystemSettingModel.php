<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model {
  protected $table = 'system_settings';
  protected $primaryKey = 'id';

  protected $useAutoIncrement = true;

  protected $returnType = 'array';
  protected $useSoftDeletes = false;

  protected $allowedFields = ['meta_key', 'meta_value', 'label', 'field_type', 'created_at', 'updated_at'];

  protected $useTimestamps = true;
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  protected $validationRules    = [];
  protected $validationMessages = [];
  protected $skipValidation     = false;
}
