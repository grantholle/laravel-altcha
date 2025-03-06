<?php

use GrantHolle\Altcha\Altcha;

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

    $passes = \Illuminate\Support\Facades\Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new \GrantHolle\Altcha\Rules\ValidAltcha],
    ])->passes();

    expect($passes)->toBeTrue();
});

it('can fail validation with incorrect challenge', function () {
    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    $challenge['number'] = 11;
    $encoded = base64_encode(json_encode($challenge));

    $passes = \Illuminate\Support\Facades\Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new \GrantHolle\Altcha\Rules\ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});

it('can fail validation past expiration', function () {
    // Set a negative expiration time to force failure
    config()->set('altcha.expires', -10);

    $challenge = app(Altcha::class)
        ->createChallenge(number: 10);
    $challenge['number'] = 10;
    $encoded = base64_encode(json_encode($challenge));

    $passes = \Illuminate\Support\Facades\Validator::make([
        'payload' => $encoded,
    ], [
        'payload' => [new \GrantHolle\Altcha\Rules\ValidAltcha],
    ])->passes();

    expect($passes)->toBeFalse();
});
