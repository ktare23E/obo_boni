<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Permit;
use App\Mail\ClientNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Inertia\Inertia;

class PermitController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'release_date' => 'required|date',
            'status' => 'in:pending,ready,released'
        ]);

        $validated['status'] = 'ready';

        // Use Carbon to make the date human-readable for the email only
        $readableDate = Carbon::parse($validated['release_date'])->format('F j, Y');

        // Find business + get client email
        $business = Business::findOrFail($validated['business_id']);
        $business->update([
            'status' => 'For Permit Release',
            'remarks' => 'For Permit Release'
        ]);
        $clientEmail = $business->user->email;

        // Send email notification
        $details = [
            'title' => 'Building Permit Notification',
            'body'  => 'Your business permit has been processed. You can claim it at the main office on ' . $readableDate . '.'
        ];

        Mail::to($clientEmail)->queue(new ClientNotification($details));

        // Create the permit record using the validated data
        Permit::create($validated);

        return back()->with('success', 'Permit created successfully.');
    }

    public function index(){
        $permits = Permit::with('business.user')->get();

        // return $permits;

        return Inertia::render('Admin/Permit/Index',[
            'permits' => $permits
        ]);
    }

    public function staffList(){
        $permits = Permit::with('business.user')->get();

        return Inertia::render('Staff/Permit/Index',[
            'permits' => $permits
        ]);
    }

    public function release(Permit $permit)
    {
        // Update the permit status and release date
        $permit->update([
            'status' => 'released',
            'release_date' => Carbon::now(),
        ]);

        $building = $permit->business;

        $building->update([
            'status' => 'Permit Released',
            'remarks' => 'Permit Released'
        ]);

        return redirect()->back()->with('success', 'Permit released successfully.');
    }
}
