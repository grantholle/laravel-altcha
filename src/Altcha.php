<?php

namespace GrantHolle\Altcha;

use AltchaOrg\Altcha\BaseChallengeOptions;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;
use GrantHolle\Altcha\Exceptions\InvalidAlgorithmException;

class Altcha
{
    public \AltchaOrg\Altcha\Altcha $altcha;

    public function __construct(
        protected string $algorithm,
        protected string $key,
        protected int $rangeMax,
    ) {
        $this->altcha = new \AltchaOrg\Altcha\Altcha($this->key);
    }

    public function createChallenge(?int $expiration = null): array
    {
        $algorithm = match (strtolower($this->algorithm)) {
            'sha-1' => Algorithm::SHA1,
            'sha-256' => Algorithm::SHA256,
            'sha-512' => Algorithm::SHA512,
            default => throw new InvalidAlgorithmException('Algorithm must be set to SHA-1, SHA-256 or SHA-512.'),
        };

        if ($expiration || is_int(config('altcha.expires'))) {
            $seconds = config('altcha.expires', $expiration);
        }

        $challenge = $this->altcha->createChallenge(new ChallengeOptions(
            algorithm: $algorithm,
            maxNumber: $this->rangeMax ?? BaseChallengeOptions::DEFAULT_MAX_NUMBER,
            expires: (new \DateTimeImmutable())->add(new \DateInterval("PT{$seconds}S")),
            saltLength: config('altcha.salt_length')
        ));

        return get_object_vars($challenge);
    }

    /**
     * @var array|string $payload
     * @var bool $checkExpires
     * @return bool
     */
    public function verifySolution(mixed $payload, bool $checkExpires = true): bool
    {
        return $this->altcha->verifySolution($payload, $checkExpires);
    }
}
