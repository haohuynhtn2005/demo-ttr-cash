<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model {
  protected $table            = 'user_roles';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;
  protected $protectFields    = true;
  protected $allowedFields    = ['name', 'description'];
  protected $useTimestamps = true;
}
