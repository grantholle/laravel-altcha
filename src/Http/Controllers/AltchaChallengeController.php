<?php

namespace GrantHolle\Altcha\Http\Controllers;

use GrantHolle\Altcha\Altcha;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AltchaChallengeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return app(Altcha::class)
            ->createChallenge();
    }
}
