<?php

namespace App\Repositories\Interfaces;

use App\User;

interface UserRepositoryInterface
{
    public function datatable();

    public function create($data);

    public function details(User $user);

    public function update(User $user, $data);

    public function delete(User $user);

    public function updatePassword(User $user, $data);

    public function updatePin(User $user, $data);
}