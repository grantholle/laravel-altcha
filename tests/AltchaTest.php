<?php

use GrantHolle\Altcha\Altcha;
use GrantHolle\Altcha\Rules\ValidAltcha;
use Illuminate\Support\Facades\Validator;

it('can generate challenge', function () {
    $challenge = app(Altcha::class)->createChallenge();

    expect($challenge)->toHaveKeys([
        'algorithm',
        'challenge',
        'salt',
        'signature',
    ]);
});

it('can use endpoint to get challenge', function () {
    $this->get(route('altcha-challenge'))
        ->assertOk()
        ->assertJsonStructure([
            'algorithm',
            'challenge',
            'salt',
            'signature',
        ]);
});

it('can validate challenge using rule', function () {
    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    sleep(1);
    $challenge['number'] = 10;
    $encoded = base64_encode(json_encode($challenge));

    $passes = Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeTrue();
});

it('can fail validation with incorrect challenge', function () {
    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    $challenge['number'] = 11;
    $encoded = base64_encode(json_encode($challenge));

    $passes = Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});

it('can fail validation past expiration', function () {
    // Set a negative expiration time to force failure
    config()->set('altcha.expires', -10);

    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    $challenge['number'] = 10;
    expect($challenge['salt'])->toContain('expires');
    $encoded = base64_encode(json_encode($challenge));

    $passes = Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});

it('does not require expires parameter', function () {
    config()->set('altcha.expires', null);

    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    $challenge['number'] = 10;

    expect($challenge['salt'])->not->toContain('expires');

    $passes = Validator::make([
        'payload' => base64_encode(json_encode($challenge)),
    ], [
        'payload' => [new ValidAltcha],
    ])->passes();

    expect($passes)->toBeTrue();
});
