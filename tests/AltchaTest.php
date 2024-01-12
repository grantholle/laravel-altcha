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
