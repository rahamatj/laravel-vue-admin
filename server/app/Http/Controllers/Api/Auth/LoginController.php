<?php

namespace App\Http\Controllers\Api\Auth;

use App\Client;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where($this->username(), $request->email)->first();

        if ($user == null)
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);

        if (!$this->isIpAllowed($request, $user))
            return response()->json([
                'message' => 'Login from this IP: ' . $request->ip . ' is not allowed.'
            ], 401);

        if (!$this->isClientAllowed($request, $user))
            return response()->json([
                'message' => 'Login from this client is not allowed.'
            ], 401);

        if ($this->attemptLogin($request))
        {
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function isClientAllowed($request, $user) {
        if ($user->is_client_lock_enabled) {
            $client = Client::where([
                ['user_id', $user->id],
                ['fingerprint', $request->fingerprint]
            ])->first();

            $userClientsCount = Client::where('user_id', $user->id)->count();

            if ($client && !$client->is_enabled)
                return false;

            if ($userClientsCount == $user->clients_allowed && !$client)
                return false;
        }

        return true;
    }

    protected function isIpAllowed(Request $request, $user) {
        if ($user->is_ip_lock_enabled) {
            $client = Client::where([
                ['user_id', $user->id],
                ['ip', $request->ip]
            ])->first();

            if ($client == null && Client::all()->count() > 0)
                return false;

            return true;
        }

        return true;
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt($this->credentials($request));
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    protected function sendLoginResponse(Request $request)
    {
        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return new Response('', 204);
    }

    protected function authenticated(Request $request, $user)
    {
        Client::updateOrcreate(['fingerprint' => $request->fingerprint], [
            'user_id' => $user->id,
            'client' => $request->client,
            'platform' => $request->platform,
            'ip' => $request->ip,
            'logged_in_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message' => 'Login successful!',
            'token' => $user->createToken(config('app.name'))->accessToken,
            'user' => $user
        ]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'fingerprint' => 'required|string',
            'client' => 'required|string',
            'platform' => 'required|string',
            'ip' => 'required|string'
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->user()->tokens()->delete();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return new Response('', 204);
    }

    protected function loggedOut(Request $request)
    {
        //
    }

    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
