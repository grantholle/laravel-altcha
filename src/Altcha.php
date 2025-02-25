<?php

namespace GrantHolle\Altcha;

use GrantHolle\Altcha\Exceptions\InvalidAlgorithmException;
use Illuminate\Support\Str;

class Altcha
{
    public function __construct(
        protected string $algorithm,
        protected string $key,
        protected int $rangeMin,
        protected int $rangeMax,
    ) {}

    public function createChallenge(?string $salt = null, ?int $number = null): array
    {
        $salt = $salt ?? bin2hex(random_bytes(config('altcha.salt_length')));
        $number = $number ?? random_int($this->rangeMin, $this->rangeMax);

        $algorithm = match (strtolower($this->algorithm)) {
            'sha-256' => 'sha256',
            'sha-384' => 'sha384',
            'sha-512' => 'sha512',
            default => throw new InvalidAlgorithmException('Algorithm must be set to SHA-256, SHA-384 or SHA-512.'),
        };

        if (is_int(config('altcha.expires'))) {
            $salt .= '?expires='.time() + config('altcha.expires');
        }

        $challenge = hash($algorithm, $salt.$number);
        $signature = hash_hmac($algorithm, $challenge, $this->key);

        return [
            'algorithm' => $this->algorithm,
            'challenge' => $challenge,
            'salt' => $salt,
            'signature' => $signature,
        ];
    }

    public function validPayload(string $payload): bool
    {
        $json = json_decode(base64_decode($payload), true);

        if ($json !== null) {
            $salt = Str::before($json['salt'], '?');
            parse_str(Str::after($json['salt'], '?'), $params);

            $check = $this->createChallenge($salt, $json['number']);

            if (isset($params['expires']) && $params['expires'] < time()) {
                return false;
            }

            return $json['algorithm'] === $check['algorithm']
                && $json['challenge'] === $check['challenge']
                && $json['signature'] === $check['signature'];
        }

        return false;
    }
}
