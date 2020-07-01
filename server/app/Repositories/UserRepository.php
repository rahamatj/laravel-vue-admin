<?php

namespace App\Repositories;

use App\Datatable\Datatable;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        $query = User::select([
            'id',
            'name',
            'email',
            'mobile_number',
            'is_otp_verification_enabled_at_login',
            'otp_type'
        ]);

        $datatable = new Datatable($query, [
            'name',
            'email',
            'mobile_number',
            'otp_type'
        ]);

        return $datatable->get();
    }
}