<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends ResourceController
{
    use ResponseTrait;

    public function profile()
    {
        $key = getenv('jwt.secret');
        $header = $this->request->getHeaderLine("Authorization");
        $token = null;

        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        // check if token is null or empty
        if (is_null($token) || empty($token)) {
            return $this->failUnauthorized('Access denied');
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $model = new UserModel();
            // Find user by email from decoded token
            $user = $model->where('email', $decoded->email)->first();
            // Remove password from response
            unset($user['password']);
            return $this->respond($user);
        } catch (\Exception $ex) {
            return $this->failUnauthorized('Access denied, invalid token');
        }
    }
}