<?php

namespace GrantHolle\Altcha;

use AltchaOrg\Altcha\BaseChallengeOptions;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;

class Altcha
{
    public function __construct(
        protected \AltchaOrg\Altcha\Altcha $altcha,
        protected Algorithm $algorithm,
        protected int $rangeMax,
        protected int $saltLength,
        protected ?int $expires = null,
    ) {
        //
    }

    /**
     * @var int|null $expiration
     * @return array
     */
    public function createChallenge(?int $expiration = null): array
    {
        $seconds = $expiration ?? $this->expires;

        $challenge = $this->altcha->createChallenge(new ChallengeOptions(
            algorithm: $this->algorithm,
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
