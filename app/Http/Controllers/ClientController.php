<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Requirement;
use App\Models\RequirementSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render("Dashboard");
    }

    public function building()
    {
        $user = Auth::user();
        $buildings = Business::where('user_id', $user->id)->get();

        return Inertia::render("Buildings", [
            'buildings' => $buildings
        ]);
    }

    public function createBuilding()
    {
        $requirements = Requirement::with('samples')->get();

        // return $requirements;

        return Inertia::render('CreateBuilding', [
            'requirements' => $requirements
        ]);
    }

    public function show(Business $business)
    {
        $submissions = RequirementSubmission::with('requirement')
            ->where('business_id', $business->id)
            ->get();
        // return $submissions;
        return Inertia::render('ViewBuilding', [
            'building' => $business,
            'submissions' => $submissions
        ]);
    }

    public function profile()
    {

        return Inertia::render('Profile', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
    
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'      => ['nullable', 'string', 'max:20'],
            'address'    => ['nullable', 'string', 'max:255'],
        ]);
    
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->contact_number = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;
    
        $user->save();
    
        return back()->with('success', 'Profile updated successfully!');
    }
    

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }
}
