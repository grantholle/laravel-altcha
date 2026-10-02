<?php

namespace GrantHolle\Altcha;

use AltchaOrg\Altcha\V1\BaseChallengeOptions;
use AltchaOrg\Altcha\V1\ChallengeOptions;
use AltchaOrg\Altcha\V1\Hasher\Algorithm;
use GrantHolle\Altcha\Exceptions\InvalidAlgorithmException;
use Illuminate\Support\Facades\Cache;

class Altcha
{
    public function __construct(
        protected \AltchaOrg\Altcha\V1\Altcha $altcha,
        protected string $algorithm,
        protected int $rangeMax,
        protected int $saltLength,
        protected ?int $expires = null,
        protected bool $singleUse = false,
    ) {
        //
    }

    /**
     * @var int|null
     *
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
            expires: $seconds ? (new \DateTimeImmutable)->add(new \DateInterval("PT{$seconds}S")) : null,
            saltLength: $this->saltLength,
        ));

        return get_object_vars($challenge);
    }

    /**
     * @var array|string
     * @var bool
     */
    public function verifySolution(mixed $payload): bool
    {
        if (! $this->altcha->verifySolution($payload)) {
            return false;
        }

        return ! $this->singleUse || $this->spend($payload);
    }

    /**
     * Remembers a verified solution so it cannot be submitted again.
     * add() is atomic, so two requests racing the same payload cannot both win.
     */
    protected function spend(mixed $payload): bool
    {
        $data = is_string($payload) ? json_decode(base64_decode($payload, true) ?: '', true) : $payload;

        if (! is_array($data) || ! is_string($data['signature'] ?? null)) {
            return false;
        }

        // Keep the record through the challenge's last valid second (the
        // verifier accepts time() == expires). A challenge without an expiry
        // gets a year: a null TTL would skip the store's atomic add().
        parse_str(parse_url($data['salt'] ?? '', PHP_URL_QUERY) ?? '', $params);
        $ttl = isset($params['expires']) ? max(1, (int) $params['expires'] - time() + 1) : 31_536_000;

        return Cache::add('altcha:spent:'.hash('sha256', $data['signature']), true, $ttl);
    }
}
