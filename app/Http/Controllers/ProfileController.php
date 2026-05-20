<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\KycWelcome;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('settings.services', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('settings.services')->with('status', 'profile-updated');
    }

    /**
     * Update required profile information for onboarding.
     */
    public function updateRequired(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $rules = [
            'first_name' => empty($user->first_name) ? 'required|string|max:255|min:2' : 'nullable|string|max:255|min:2',
            'last_name' => empty($user->last_name) ? 'required|string|max:255|min:2' : 'nullable|string|max:255|min:2',
            'middle_name' => 'nullable|string|max:255',
            'phone_no' => empty($user->phone_no) ? 'required|string|max:15|min:10|regex:/^[0-9+\-\s()]+$/|unique:users,phone_no,' . $user->id : 'nullable|string|max:15|min:10|regex:/^[0-9+\-\s()]+$/|unique:users,phone_no,' . $user->id,
            'lga' => empty($user->lga) ? 'required|string|max:255' : 'nullable|string|max:255',
            'state' => empty($user->state) ? 'required|string|max:255' : 'nullable|string|max:255',
            'address' => empty($user->address) ? 'required|string|max:500' : 'nullable|string|max:500',
            'pin' => empty($user->pin) ? 'required|digits:5' : 'nullable|digits:5',
            'termsCheck' => 'required|string|max:500', 
        ];

        if (empty($user->bvn)) {
            $rules['bvn'] = 'required|digits:11|unique:users,bvn,' . $user->id;
        } else {
            $rules['bvn'] = 'nullable|digits:11|unique:users,bvn,' . $user->id;
        }

        $validated = $request->validate($rules, [
            'phone_no.unique' => 'The phone number has already been taken by another user. Please use a different phone number.',
            'bvn.unique' => 'The BVN has already been taken by another user. Please verify your BVN or contact support.',
            'pin.required' => 'Transaction PIN is required.',
            'pin.digits' => 'PIN must be exactly 5 digits.',
        ]);

        try {
            $updateData = [];

            if (!empty($validated['first_name'])) {
                $updateData['first_name'] = $validated['first_name'];
            }
            if (!empty($validated['last_name'])) {
                $updateData['last_name'] = $validated['last_name'];
            }
            if (isset($validated['middle_name'])) {
                $updateData['middle_name'] = $validated['middle_name'];
            }
            if (!empty($validated['phone_no'])) {
                $updateData['phone_no'] = $validated['phone_no'];
            }
            if (!empty($validated['lga'])) {
                $updateData['lga'] = $validated['lga'];
            }
            if (!empty($validated['state'])) {
                $updateData['state'] = $validated['state'];
            }
            if (!empty($validated['address'])) {
                $updateData['address'] = $validated['address'];
            }
            if (!empty($validated['pin'])) {
                $updateData['pin'] = bcrypt($validated['pin']);
            }
            if (!empty($validated['bvn'])) {
                $updateData['bvn'] = $validated['bvn'];
            }

            $user->update($updateData);

            // Send Welcome Email
            try {
                Mail::to($user->email)->send(new KycWelcome($user));
            } catch (\Exception $e) {
                // Log error but don't fail the request if email fails
                \Log::error('Failed to send KYC welcome email: ' . $e->getMessage());
            }

            return redirect()->route('dashboard')->with('success', 'Account successfully! Welcome aboard! 🎉');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Profile update database error: ' . $e->getMessage());
            // Intercept database integrity / duplicate errors
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'A user with this email, phone number, or BVN already exists. Please verify your details.';
                if (str_contains($e->getMessage(), 'phone_no')) {
                    $errorMessage = 'The phone number is already registered with another account.';
                } elseif (str_contains($e->getMessage(), 'bvn')) {
                    $errorMessage = 'The BVN is already registered with another account.';
                } elseif (str_contains($e->getMessage(), 'email')) {
                    $errorMessage = 'The email address is already registered with another account.';
                }
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
            return redirect()->back()->withInput()->with('error', 'Information update failed, contact support.');
        } catch (\Exception $e) {
            \Log::error('Profile update unexpected error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An unexpected error occurred while completing your profile. Please try again.');
        }
    }

    /**
     * Upload or update profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        try {
            // ✅ Delete old photo if exists (with improved logic)
            if ($user->photo) {
                $this->deleteOldProfilePhoto($user->photo);
            }

            // ✅ Store new image using Laravel's Storage facade
            $file = $request->file('photo');
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in storage/app/public/uploads/profile_photos
            $path = $file->storeAs('uploads/profile_photos', $fileName, 'public');
            
            // ✅ Build full HTTP link
            $fullUrl = Storage::disk('public')->url($path);

            // ✅ Save to database
            $user->update([
                'photo' => $fullUrl,
            ]);

            return back()->with('status', '✅ Profile photo updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Delete old profile photo with proper handling
     */
    private function deleteOldProfilePhoto(string $photoUrl): void
    {
        // Skip external URLs (like gravatar)
        if (Str::startsWith($photoUrl, 'http') && !Str::contains($photoUrl, '/storage/')) {
            return;
        }

        try {
            // If it's a storage URL, extract the path
            if (Str::contains($photoUrl, '/storage/')) {
                // Remove the base URL to get the storage path
                $baseUrl = config('app.url') . '/storage/';
                $path = str_replace($baseUrl, '', $photoUrl);
                Storage::disk('public')->delete($path);
            } 
            // If it's already a storage path (not full URL)
            elseif (Storage::disk('public')->exists($photoUrl)) {
                Storage::disk('public')->delete($photoUrl);
            }
            // For old-style public/uploads paths
            elseif (Str::contains($photoUrl, '/uploads/')) {
                // Extract filename from URL
                $filename = basename($photoUrl);
                Storage::disk('public')->delete('uploads/profile_photos/' . $filename);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Failed to delete old profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Check if user needs to complete profile (for modal trigger)
     */
    public function checkProfileCompletion(): bool
    {
        $user = Auth::user();
        
        // Define required fields that must be filled
        $requiredFields = [
            'first_name', 'last_name', 'phone_no', 'lga', 
            'state', 'address', 'bvn'
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Update additional profile information (only if not already set).
     */
    public function updateAdditionalInfo(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'nin' => 'nullable|digits:11|unique:users,nin,' . $user->id,
            'bvn' => 'nullable|digits:11|unique:users,bvn,' . $user->id,
            'state' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $updates = [];

        // Only allow updating if the field is currently empty in the database
        if (empty($user->business_name) && !empty($validated['business_name'])) {
            $updates['business_name'] = $validated['business_name'];
        }
        if (empty($user->nin) && !empty($validated['nin'])) {
            $updates['nin'] = $validated['nin'];
        }
        if (empty($user->bvn) && !empty($validated['bvn'])) {
            $updates['bvn'] = $validated['bvn'];
        }
        if (empty($user->state) && !empty($validated['state'])) {
            $updates['state'] = $validated['state'];
        }
        if (empty($user->lga) && !empty($validated['lga'])) {
            $updates['lga'] = $validated['lga'];
        }
        if (empty($user->address) && !empty($validated['address'])) {
            $updates['address'] = $validated['address'];
        }

        if (!empty($updates)) {
            $user->update($updates);
            return back()->with('status', 'Additional information updated successfully.');
        }

        return back()->with('info', 'No changes made or fields are already set.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Update the user's transaction PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'pin' => ['required', 'digits:5', 'confirmed'],
        ]);

        $request->user()->update([
            'pin' => bcrypt($validated['pin']),
        ]);

        return back()->with('status', 'pin-updated');
    }
}