<?php

namespace App\Repositories;

use App\Client;
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
        ])->addSelect([
            'last_logged_in_at' => Client::select('logged_in_at')
                ->whereColumn('user_id', 'users.id')
                ->latest()
                ->take(1)
        ])->where('id', '!=', Auth::id());

        $datatable = new Datatable($query);

        $datatable->latest();
        $datatable->filterBy([
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

    public function details(User $user)
    {
        return User::join('users as parent', 'parent.id', '=', 'users.parent_id')
            ->select([
                'users.*',
                'parent.name as parent_name',
            ])
            ->addSelect([
                'last_logged_in_at' => Client::select('logged_in_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->take(1)
            ])
            ->addSelect([
                'clients_count' => Client::selectRaw('COUNT(id)')
                    ->whereColumn('user_id', 'users.id')
            ])
            ->find($user->id);
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