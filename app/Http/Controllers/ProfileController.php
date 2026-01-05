<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        // print_r($request->all());
        // die;

        $user = $request->user();
        $validated = $request->validated();

        // Handle photo upload manually
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            // Validate file size manually (even though validated by FormRequest)
            if ($photo->getSize() > 5 * 1024 * 1024) { // 5MB
                return back()->withErrors(['photo' => 'The uploaded photo must not exceed 5MB.']);
            }

            // Delete old photo if exists
            if ($user->photo && file_exists(public_path('uploads/profile_photos/' . $user->photo))) {
                unlink(public_path('uploads/profile_photos/' . $user->photo));
            }

            // Create a new unique photo name
            $photoName = time() . '_' . $photo->getClientOriginalName();

            // Move the uploaded file to public/uploads/profile_photos
            $photo->move(public_path('uploads/profile_photos'), $photoName);

            // Set the new photo name (we will save later)
            $validated['photo'] = $photoName;
        } else {
            // If no new photo uploaded, prevent overwriting photo field
            unset($validated['photo']);
        }

        // Fill all validated fields (including photo if updated)
        $user->fill($validated);

        // If email changed, reset verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // ✅ Handle password correctly
        // if (empty($validated['password'])){
        //     unset($validated['password']); // If password is empty, remove it from validated data
        // } else {
        //     $validated['password'] = bcrypt($validated['password']); // Encrypt password if provided
        // }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Profile updated successfully');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
