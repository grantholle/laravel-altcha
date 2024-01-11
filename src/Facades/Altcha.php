<?php

namespace Grant Holle\Altcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Grant Holle\Altcha\Altcha
 */
class Altcha extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Grant Holle\Altcha\Altcha::class;
    }
}
