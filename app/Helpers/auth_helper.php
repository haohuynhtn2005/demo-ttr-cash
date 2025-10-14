<?php

use App\Models\UserRoleModel;

if (!function_exists('isAdmin')) {
    function isAdmin(int $roleId): bool {
        $roleModel = new UserRoleModel();
        $role = $roleModel->find($roleId);

        if ($role && $role['key'] === 'ADMIN') {
            return true;
        }

        return false;
    }
}
