<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\WebAuthn;
use Illuminate\Support\Facades\Auth;

class WebAuthnConfirmController extends Controller
{
    /**
     * Generate WebAuthn options for an existing user (Step-up).
     */
    public function options(AssertionRequest $request)
    {
        return $request->toVerify(Auth::user());
    }

    /**
     * Verify the biometric assertion for the current user.
     */
    public function verify(AssertedRequest $request)
    {
        if ($request->login()) {
            // Store authorization for 5 minutes
            session(['biometric_verified_at' => now()->timestamp]);
            
            return response()->json([
                'success' => true,
                'message' => 'Biometric verified successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Biometric verification failed.'
        ], 422);
    }
}
