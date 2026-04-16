<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     */
    public function options(AssertionRequest $request): Responsable
    {
        return $request->toVerify($request->validate(['email' => 'nullable|email|string']));
    }

    /**
     * Log the user in.
     */
    public function login(AssertedRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = $request->user();

        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user, true);
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Biometric verification failed'], 422);
    }
}
