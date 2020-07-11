<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Store;
use App\Http\Requests\User\Update;
use App\Http\Requests\User\UpdatePassword;
use App\Http\Requests\User\UpdatePin;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
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
    public function store(Store $request, UserRepositoryInterface $userRepository)
    {
        return response()->json([
           'message' => 'User created successfully!',
           'data' => new UserResource($userRepository->create($request->validated()))
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, User $user, UserRepositoryInterface $userRepository)
    {
        $userRepository->update($user, $request->validated());

        return response()->json([
           'message' => 'User updated successfully!',
           'data' => new UserResource($user)
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

    public function updatePassword(UpdatePassword $request, User $user, UserRepositoryInterface $userRepository)
    {
        $userRepository->updatePassword($user, $request->validated());

        return response()->json([
           'message' => 'Password updated successfully!'
        ]);
    }

    public function updatePin(UpdatePin $request, User $user, UserRepositoryInterface $userRepository)
    {
        $userRepository->updatePin($user, $request->validated());

        return response()->json([
            'message' => 'PIN updated successfully!'
        ]);
    }
}
