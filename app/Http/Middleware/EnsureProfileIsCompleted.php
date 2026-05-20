<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user is logged in, check if they completed their KYC profile
        if ($user) {
            // Check if user has empty fields for essential profile details
            if (
                empty($user->first_name) || 
                empty($user->last_name) || 
                empty($user->phone_no) || 
                empty($user->state) || 
                empty($user->lga) || 
                empty($user->address) || 
                empty($user->pin) || 
                empty($user->bvn)
            ) {
                // If it is an API request or expects JSON, return a JSON error
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Please complete your KYC profile to access this feature.'
                    ], 403);
                }

                // If they are not on the dashboard page, and not attempting to submit profile update or logging out, redirect to dashboard
                if (!$request->is('dashboard') && !$request->routeIs('profile.updateRequired') && !$request->routeIs('logout')) {
                    return redirect()->route('dashboard')->with('error', 'Please complete your KYC profile to access this feature.');
                }
            }
        }

        return $next($request);
    }
}
