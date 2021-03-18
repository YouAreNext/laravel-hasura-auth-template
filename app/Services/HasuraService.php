<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class HasuraService
{
  /**
   * Create a new token.
   *
   * @return string
   */
  public function jwt(string $model, string $userId)
  {
    $payload = [
      'iss' => 'Hasura-JWT-Auth',
      'sub' => (string) $userId,
      'model' => $model,
      'iat' => time(),
      'exp' => time() + 60 * 60 * 24 * 7,
      'https://hasura.io/jwt/claims' => [
        'x-hasura-user-id' => (string) $userId,
        'x-hasura-default-role' => 'ADMIN',
        'x-hasura-allowed-roles' => [
          'ADMIN'
        ]
      ]
    ];

    return JWT::encode($payload, env('JWT_SECRET'));
  }
}
