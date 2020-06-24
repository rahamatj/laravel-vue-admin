<?php

namespace App\Http\Controllers\Api\Auth;

use App\Client;
use App\Http\Controllers\Controller;
use App\Otp\Otp;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'isAuthenticated']);
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
                'message' => 'Login from this IP: ' . $request->getClientIp() . ' is not allowed.'
            ], 401);

        if (!$this->isClientAllowed($request, $user))
            return response()->json([
                'message' => 'Login from this client is not allowed.'
            ], 401);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request))
        {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

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
                ['ip', $request->getClientIp()]
            ])->first();

            if ($client == null && Client::all()->count() > 0)
                return false;

            return true;
        }

        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return new Response('', 204);
    }

    protected function authenticated(Request $request, $user)
    {
        Client::updateOrcreate([
            'fingerprint' => $request->fingerprint,
            'user_id' => $user->id
        ], [
            'client' => $request->client,
            'platform' => $request->platform,
            'ip' => $request->getClientIp(),
            'logged_in_at' => date("Y-m-d H:i:s")
        ]);

        if ($user->is_otp_verification_enabled_at_login)
            Otp::send();

        return response()->json([
            'message' => 'Login successful!',
            'token' => $user->createToken(config('app.name'))->accessToken,
            'user' => $user->toArray()
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
}
