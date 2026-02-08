<?php

declare(strict_types=1);

namespace Services;

class JwtService
{
  private string $secret;
  private int $expiration;

  public function __construct()
  {
    $this->secret = $_ENV['JWT_SECRET'];
    $this->expiration = (int) $_ENV['JWT_EXPIRATION'];
  }

  public function generateToken(array $payload): string
  {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload['iat'] = time();
    $payload['exp'] = time() + $this->expiration;
    $payload = json_encode($payload);

    $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->secret, true);
    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64Header . "." . $base64Payload . "." . $base64Signature;
  }

  public function verifyToken(string $token): ?array
  {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
      return null;
    }

    [$header, $payload, $signature] = $parts;

    $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
    $validSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

    if (!hash_equals($signature, $validSignature)) {
      return null;
    }

    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

    if ($payload['exp'] < time()) {
      return null;
    }

    return $payload;
  }
}
