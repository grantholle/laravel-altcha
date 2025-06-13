<?php

namespace GrantHolle\Altcha\Http\Controllers;

use GrantHolle\Altcha\Altcha;
use Illuminate\Routing\Controller;

class AltchaChallengeController extends Controller
{
    public function __invoke(Altcha $altcha)
    {
        return $altcha->createChallenge();
    }
}
