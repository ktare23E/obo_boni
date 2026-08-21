<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function profile(){
        $user = Auth::user();
        
        return Inertia::render('Admin/Profile',[
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match']);
        }

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully');
    }
}
