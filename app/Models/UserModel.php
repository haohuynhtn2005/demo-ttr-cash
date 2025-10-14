<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
  protected $table            = 'users';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;
  protected $protectFields    = true;
  protected $allowedFields    = ['name', 'email', 'password'];

  // Dates
  protected $useTimestamps = true;

  /**
   * Get the roles for a user.
   *
   * @param int $userId
   * @return array
   */
  public function getRole(int $userId): array {
    return $this->select('user_roles.name')
      ->join('user_roles', 'user_roles.id = users.role_id')
      ->where('users.id', $userId)
      ->findAll();
  }
}
