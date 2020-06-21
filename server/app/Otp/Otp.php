<?php

namespace App\Otp;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Otp
{
    protected static $otpTypeColumnName = 'otp_type';

    public static function type($user)
    {
        $namespace = (new \ReflectionClass(static::class))->getNamespaceName();

        $otpType = $namespace . '\\Types\\' . Str::studly($user->{self::$otpTypeColumnName});

        return new $otpType($user);
    }

    public static function __callStatic($method, $args)
    {
        return static::type(Auth::user())->$method(...$args);
    }
}