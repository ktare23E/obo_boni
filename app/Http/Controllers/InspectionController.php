<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientNotification;
use App\Models\User;
use App\Mail\AdminNotification;

class InspectionController extends Controller
{
    public function index(){
        $user = Auth::user();
        $inspections = Inspection::with('business.user','schedule')
                            ->where('user_id',$user->id)
                            ->get();

        // return $inspections;

        return Inertia::render('Inspector/Inspection/Index',[
            'inspections' => $inspections
        ]);
    }

    public function adminInspection(){
        $inspections = Inspection::with('business.user','user','schedule')
                        ->get();

        // return $inspections;
        return Inertia::render('Admin/Inspection/Index',[
            'inspections' => $inspections
        ]);
    }

    public function staffInspection(){
        $inspections = Inspection::with('business.user','user','schedule')
                        ->get();

        // return $inspections;
        return Inertia::render('Staff/Inspection/Index',[
            'inspections' => $inspections
        ]);
    }

    public function approve(Business $business)
    {
        $business->inspection->update([
            'status' => 'Passed',
            'remarks' => 'Approved by inspector',
        ]);

        $business->update([
            'status' => 'Approved',
            'remarks' => 'For Creating Building Permit',
        ]); 

        $owner = $business->user;
        $full_name = $owner->first_name.' '.$owner->last_name;

        $admin = User::where('user_type','Admin')->first();
        
        //inspector data
        $inspector = $business->inspection;
        $inspector_full_name = $inspector->user->first_name. ' '.$inspector->user->last_name;

        $client_email = $business->user->email;
        $details = [
            'title' => 'Building Permit Notification',
            'body' => 'Please do prepare a building permit for the '.$business->business_name.' under the owner name of '.$full_name.',this building has been succeffully inpsected by '.$inspector_full_name.'.'
        ];

        Mail::to($admin->email)->send(new AdminNotification($details));
    
        return back()->with('success', 'Business approved successfully!');
    }
    
    public function reject(Business $business, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:255']);
    
        $business->inspection->update([
            'status' => 'Failed',
            'remarks' => $request->reason,
        ]);

        $client_email = $business->user->email;
        $details = [
            'title' => 'Building Inspection Result',
            'body' => 'Your application for building permit is failed due to the file error you submitted and the inspector has these reason '.$request->reason.'.'
        ];

        Mail::to($client_email)->send(new ClientNotification($details));

    
        return back()->with('success', 'Business rejected with reason.');
    }
    
}
