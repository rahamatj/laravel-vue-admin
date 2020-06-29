<?php

namespace App\Repositories;

use App\Datatable\Datatable;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        $datatable = new Datatable(User::query(), [
            'name',
            'email',
            'mobile_number',
            'otp_type'
        ]);

        return $datatable->get();
    }
}