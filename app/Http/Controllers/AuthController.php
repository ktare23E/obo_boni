<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientNotification;
use App\Models\ClientOTP;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            switch ($user->user_type) {
                case 'Admin':
                    return redirect()->intended(route('admin_dashboard'));
                case 'Inspector':
                    return redirect()->intended(route('inspector_dashboard'));
                case 'Staff':
                    return redirect()->intended(route('staff_dashboard'));
                // default:
                //     return redirect()->intended(route('welcome'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }


    public function destroy(Request $request){

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register(Request $request)
    {
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'address'    => ['required'],
            'contact_number'    => ['required'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'   => ['required', Rules\Password::defaults()]
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'address'    => $request->address,
            'email'      => $request->email,
            'contact_number' => $request->contact_number,
            'user_type'  => 'Client',
            'status'     => 'Inactive',
            'password'   => Hash::make($request->password),
        ]);

           // Generate 6-digit OTP
        $otpCode = rand(100000, 999999);

        $user->otp()->create([
            'otp' => $otpCode
        ]);


        $details = [
            'title' => 'Client OTP',
            'body' => 'Your OTP is ' .$otpCode
        ];

        Mail::to($user->email)->queue(new ClientNotification($details));


        return redirect()->route('client_otp',[
            'user' => $user
        ]);
    }

}
