<?php

namespace GrantHolle\Altcha;

use AltchaOrg\Altcha\BaseChallengeOptions;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;
use GrantHolle\Altcha\Exceptions\InvalidAlgorithmException;

class Altcha
{
    public function __construct(
        protected \AltchaOrg\Altcha\Altcha $altcha,
        protected string $algorithm,
        protected int $rangeMax,
        protected int $saltLength,
        protected ?int $expires = null,
    ) {
        //
    }

    /**
     * @var int|null $expiration
     * @return array
     * @throws \GrantHolle\Altcha\Exceptions\InvalidAlgorithmException
     */
    public function createChallenge(?int $expiration = null): array
    {
        $seconds = $expiration ?? $this->expires;
        $algorithm = match (strtolower($this->algorithm)) {
            'sha-1' => Algorithm::SHA1,
            'sha-256' => Algorithm::SHA256,
            'sha-512' => Algorithm::SHA512,
            default => throw new InvalidAlgorithmException('Algorithm must be set to SHA-1, SHA-256 or SHA-512.'),
        };

        $challenge = $this->altcha->createChallenge(new ChallengeOptions(
            algorithm: $algorithm,
            maxNumber: $this->rangeMax ?? BaseChallengeOptions::DEFAULT_MAX_NUMBER,
            expires: $seconds ? (new \DateTimeImmutable())->add(new \DateInterval("PT{$seconds}S")) : null,
            saltLength: $this->saltLength,
        ));

        return get_object_vars($challenge);
    }

    /**
     * @var array|string $payload
     * @var bool $checkExpires
     * @return bool
     */
    public function verifySolution(mixed $payload): bool
    {
        return $this->altcha->verifySolution($payload);
    }
}
