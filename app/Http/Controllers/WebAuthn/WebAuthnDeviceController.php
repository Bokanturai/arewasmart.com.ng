<?php

namespace App\Http\Controllers\WebAuthn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Models\WebAuthnCredential;

class WebAuthnDeviceController extends Controller
{
    /**
     * List all registered biometric devices for the current user.
     */
    public function index()
    {
        $devices = Auth::user()->webAuthnCredentials()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($devices);
    }

    /**
     * Rename a biometric device.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'alias' => 'required|string|max:255',
        ]);

        $credential = Auth::user()->webAuthnCredentials()->findOrFail($id);
        
        $credential->alias = $request->alias;
        $credential->save();

        return response()->json([
            'message' => 'Device renamed successfully',
            'alias' => $credential->alias
        ]);
    }

    /**
     * Revoke/Delete a biometric device.
     */
    public function destroy($id)
    {
        $credential = Auth::user()->webAuthnCredentials()->findOrFail($id);
        
        $credential->delete();

        return response()->json([
            'message' => 'Biometric device revoked successfully'
        ]);
    }
}
