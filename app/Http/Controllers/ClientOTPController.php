<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientNotification;
use App\Models\ClientOTP;

class ClientOTPController extends Controller
{
    public function otpPage(User $user){
        return Inertia::render('VerifyOTP',[
            'user' => $user,
            'otp' => $user->otp->otp
        ]);
    }


    public function otpLogin(Request $request)
    {
        $user = User::findOrFail($request->user_id);
    
        Auth::login($user);
    
        return redirect()->route('welcome');
    }

    public function resend(Request $request)
    {
        $user = User::find($request->user_id);

        $newOtp = rand(100000, 999999);

        // save or update OTP
        $user->otp = $newOtp;
        $user->save();

        $details = [
            'title' => 'Client OTP',
            'body' => 'Your OTP is ' .$newOtp
        ];

        Mail::to($user->email)->queue(new ClientNotification($details));


        // send SMS here
        // SmsService::send($user->phone, "Your new OTP is: " . $newOtp);

        return back()->with(['otp' => $newOtp]);
    }

    public function verify(Request $request)
    {
        // return $request->all();
        $otp = ClientOTP::where('otp',$request->otp)->first();
        $user = $otp->user;
        // update status
        $user->status = "Active";
        $user->save();

        auth()->login($user);

        return redirect()->route('welcome');
    }

}
