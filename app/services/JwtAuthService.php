<?php

namespace App\Services;

use CodeIgniter\API\ResponseTrait;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class JwtAuthService {
    use ResponseTrait;

    private string $key;
    private int $expireTime;
    private string $algorithm = 'HS256';

    public function __construct() {
        $this->key = getenv('jwt.secret');
        $this->expireTime = 3600; // Token is valid for 1 hour
    }

    /**
     * Generates a JWT for the given payload.
     *
     * @param array $payload The data to encode in the token.
     * @return string The generated JWT.
     */
    public function generateToken(array $payload): string {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expireTime;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        ]);

        return JWT::encode($tokenPayload, $this->key, $this->algorithm);
    }

    /**
     * Authenticates a user by validating the JWT from the Authorization header.
     *
     * @return array An array with 'status' and either 'user_info' on success or 'message' on failure.
     */
    public function authenticateUser(): array {
        $request = Services::request();
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return ['status' => false, 'message' => 'No token provided.'];
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algorithm));
            return ['status' => true, 'user_info' => $decoded];
        } catch (Throwable $e) {
            return ['status' => false, 'message' => 'Invalid token: ' . $e->getMessage()];
        }
    }
}
