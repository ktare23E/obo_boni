<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RequirementSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Mail\StaffNofication;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Requirement;
use App\Jobs\ProcessBusinessUploads;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function index()
    {
        $business = Business::with('user', 'permit')->get();

        // return $business;

        return Inertia::render('Admin/Business/Index', [
            'business' => $business
        ]);
    }

    public function staffList()
    {
        $business = Business::with('user', 'permit')->get();

        // return $business;

        return Inertia::render('Staff/Business/Index', [
            'business' => $business
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name'     => 'required|string|max:255',
            'address'           => 'required|string|max:255',
            'lat'           => 'required|string|max:255',
            'lng'           => 'required|string|max:255',
            'place_description'           => 'required|string|max:255',
            'type_of_business'  => 'required|string|max:255',
            'building_type'  => 'required|string|max:255',
            'image_path'        => 'nullable|file|mimes:jpg,jpeg,png',
            'files'             => 'required|array',
            'files.*'           => 'array',
            'files.*.*'         => 'file',
        ]);
    
        $user = Auth::user();

        //create reference number
        $registration_no = 'BUILD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    
        // 1) Create business
        $business = Business::create([
            'registration_no' => $registration_no,
            'business_name'   => $validated['business_name'],
            'user_id'         => $user->id,
            'address'         => $validated['address'],
            'lat'         => $validated['lat'],
            'lng'         => $validated['lng'],
            'place_description'         => $validated['place_description'],
            'type_of_business'=> $validated['type_of_business'],
            'building_type'=> $validated['building_type'],
            'image_path'      => $request->file('image_path')
                ? $request->file('image_path')->store('buildings_temp', 'public')
                : null,
            'status' => 'Under Review of Admin',
            'remarks' => 'Under Review of Admim/Staff'
        ]);
    
        // 2) Save *temporary* files and push to queue
        $tempFiles = [];
    
        foreach ($request->allFiles()['files'] ?? [] as $reqId => $group) {
            foreach ($group as $file) {
                $tempFiles[] = [
                    'requirement_id' => $reqId,
                    'temp_path'      => $file->store('temp_uploads', 'public'),
                ];
            }
        }

        // 3) Dispatch job (async processing)
        ProcessBusinessUploads::dispatch($business->id, $tempFiles);

        $details = [
            'title' => 'Building Reference',
            'body' => "This is your reference number ".$registration_no.', use this to tracking your building application.'
        ];

        Mail::to($user->email)->send(new StaffNofication($details));
    
        // 4) Return immediately (fast, no timeout)
        return back()->with('success', 'Application submitted! Files are being processed.');
    }


    public function reupload(Request $request, RequirementSubmission $submission)
    {
        $request->validate([
            'file' => 'required|file|max:2048', // adjust validation rules
        ]);

        // Delete old file if exists
        if ($submission->file_path && Storage::exists($submission->file_path)) {
            Storage::delete($submission->file_path);
        }

        // Store new file
        $path = $request->file('file')->store('requirements', 'public');

        $submission->update([
            'file_path' => $path,
            'status' => 'submitted', // reset to submitted for review
        ]);

        $requirement = $submission->requirement;
        $requirement_name = $requirement->title;

        $building = $submission->business;
        $business_name = $building->business_name;

        $staff = User::where('user_type', 'Staff')
            ->where('status', 'Active')
            ->first();
        $staff_email = $staff->email;

        $details = [
            'title' => 'Re-upload Requirement Notification',
            'body' => $business_name . ' has re-upload the ' . $requirement_name . ' please do check the submissions again of ' . $business_name
        ];

        Mail::to($staff_email)->send(new StaffNofication($details));


        return back()->with('success', 'File re-uploaded successfully!');
    }
}
