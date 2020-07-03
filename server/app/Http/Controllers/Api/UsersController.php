<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepositoryInterface $userRepository)
    {
        return UserResource::collection($userRepository->datatable());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request, UserRepositoryInterface $userRepository)
    {
        return response()->json([
           'message' => 'User created successfully!',
           'data' => new UserResource($userRepository->create($request->validated()))
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, User $user, UserRepositoryInterface $userRepository)
    {
        $userRepository->update($user, $request->validated());

        return response()->json([
           'message' => 'User updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, UserRepositoryInterface $userRepository)
    {
        $userRepository->delete($user);

        return response()->json([
           'message' => 'User deleted successfully!'
        ]);
    }
}
