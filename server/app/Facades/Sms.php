<?php

namespace App\Facades;

use App\Sms\SmsFake;
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