<?php

namespace App\Sms\Facades;

use App\Sms\Fakes\Sms as SmsFake;
use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    public static function fake()
    {
        static::swap($fake = new SmsFake);

        return $fake;
    }

    protected static function getFacadeAccessor() { return 'sms'; }
}