<?php

namespace App\Repositories;

use App\Datatable\Datatable;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function datatable()
    {
        $query = User::select([
            'id',
            'name',
            'email',
            'mobile_number',
            'is_otp_verification_enabled_at_login',
            'otp_type',
            'created_at'
        ])->where('id', '!=', Auth::id());

        $datatable = new Datatable($query);

        $datatable->latest();
        $datatable->filterColumns([
            'name',
            'email',
            'mobile_number',
            'otp_type',
            'created_at'
        ]);

        return $datatable->get();
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update(User $user, $data)
    {
        return $user->update($data);
    }

    public function delete(User $user)
    {
        return $user->delete();
    }

    public function updatePassword(User $user, $data)
    {
        $user->password = $data['password'];

        return $user->save();
    }

    public function updatePin(User $user, $data)
    {
        $user->pin = $data['pin'];

        return $user->save();
    }
}