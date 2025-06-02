<?php

use GrantHolle\Altcha\Altcha;
use GrantHolle\Altcha\Rules\ValidAltcha;
use Illuminate\Support\Facades\Validator;

it('can generate challenge', function () {
    $challenge = app(Altcha::class)->createChallenge();

    expect($challenge)->toHaveKeys([
        'algorithm',
        'challenge',
        'maxNumber',
        'salt',
        'signature',
    ]);
});

it('can use endpoint to get challenge', function () {
    $this->get(route('altcha-challenge'))->assertOk()->assertJsonStructure([
        'algorithm',
        'challenge',
        'maxNumber',
        'salt',
        'signature',
    ]);
});

it('can validate challenge using rule', function () {
    $challenge = app(Altcha::class)->createChallenge();

    $passes = Validator::make([
        'payload' => solve($challenge),
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeTrue();
});

it('can fail validation with incorrect challenge', function () {
    $challenge = app(Altcha::class)->createChallenge();
    $challenge['number'] = 9999999999999;

    $passes = Validator::make([
        'payload' => base64_encode(json_encode($challenge)),
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});

it('can fail validation past expiration', function () {
    config(['altcha.expires' => 1]);
    $challenge = app(Altcha::class)->createChallenge();
    expect($challenge['salt'])->toContain('expires');

    sleep(2);
    $passes = Validator::make([
        'payload' => solve($challenge),
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});

it('does not require expires parameter', function () {
    config(['altcha.expires' => null]);
    $challenge = app(Altcha::class)->createChallenge();
    expect($challenge['salt'])->not->toContain('expires');

    $passes = Validator::make([
        'payload' => solve($challenge),
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeTrue();
});

it('can use a specific salt length', function () {
    config(['altcha.expires' => null, 'altcha.salt_length' => 12]);
    $challenge = app(Altcha::class)->createChallenge();
    expect($challenge['salt'])->toHaveLength(24);

    config(['altcha.expires' => null, 'altcha.salt_length' => 24]);
    $challenge = app(Altcha::class)->createChallenge();
    expect($challenge['salt'])->toHaveLength(48);
});

it('can use a specific expiration duration in seconds when generating a challenge', function () {
    config(['altcha.expires' => 10]);
    $now = time();

    $challenge = app(Altcha::class)->createChallenge(12);
    expect($challenge['salt'])->toContain('expires');

    $expires = Str::of($challenge['salt'])->after('?expires=')->toInteger();
    expect($expires)->toBeGreaterThanOrEqual($now + 12);
});

function solve(array $challenge): string
{
    $solution = app(\AltchaOrg\Altcha\Altcha::class)->solveChallenge(
        $challenge['challenge'],
        $challenge['salt'],
        config('altcha.algorithm'),
        $challenge['maxNumber']
    );

    $challenge['number'] = $solution->number;

    return base64_encode(json_encode($challenge));
}
