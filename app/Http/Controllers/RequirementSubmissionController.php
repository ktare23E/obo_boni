<?php

namespace App\Http\Controllers;

use App\Mail\ClientNotification;
use App\Mail\InspectionNotification;
use App\Models\RequirementSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Business;
use Illuminate\Support\Facades\Bus;
use App\Mail\SubmissionNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\InspectorSchedule;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RequirementSubmissionController extends Controller
{


    // Show all businesses with requirement submissions
    public function index()
    {
        $businesses = Business::with(['user', 'submissions.requirement'])->get();

        return Inertia::render('Admin/Submissions/Index', [
            'businesses' => $businesses
        ]);
    }

    public function view(RequirementSubmission $submission)
    {
        $user = Auth::user();
    
        if (!$user || !in_array($user->user_type, ['Admin','Staff','Inspector'])) {
            abort(403, 'Unauthorized Access');
        }
    
        // Use public disk now
        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404);
        }
    
        return Storage::disk('public')->response($submission->file_path);
    }
    

    public function staffSubmissions()
    {
        $businesses = Business::with(['user', 'submissions.requirement'])->get();


        return Inertia::render('Staff/Submissions/Index', [
            'businesses' => $businesses
        ]);
    }

    // Show submissions of a specific business
    public function show(Business $business)
    {
        $business = Business::with('submissions.requirement')->
                        find($business->id);
        // return $submissions;

        $hasFutureAvailableSchedule = InspectorSchedule::whereDate(
                            'available_date',
                            '>',
                            Carbon::today()
                        )
                        ->where('status', 'available')
                        ->exists();

        return Inertia::render('Admin/Submissions/Show', [
            'business' => $business,
            'submissions' => $business->submissions,
            'hasFutureAvailableSchedule' => $hasFutureAvailableSchedule
        ]);
    }

    public function showStaff(Business $business){
        $business = Business::with('submissions.requirement')->
        find($business->id);
        // return $submissions;

        $hasFutureAvailableSchedule = InspectorSchedule::whereDate(
            'available_date',
            '>',
            Carbon::today()
        )
        ->where('status', 'available')
        ->exists();


        return Inertia::render('Staff/Submissions/Show', [
            'business' => $business,
            'submissions' => $business->submissions,
            'hasFutureAvailableSchedule' => $hasFutureAvailableSchedule
        ]);
    }

    public function showInspector(Business $business){
        $business = Business::with('submissions.requirement','inspection.schedule')->
        find($business->id);

        // return $business;
        return Inertia::render('Inspector/Inspection/Show', [
            'business' => $business,
            'submissions' => $business->submissions
        ]);
    }

    public function approveAll(Request $request)
    {
        // Validate that 'ids' is an array
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:requirement_submissions,id'
        ]);
    
        // Update all selected submissions to 'approved'
        RequirementSubmission::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update([
                'status' => 'approved',
                'remarks' => 'Approved by Admin'
            ]);
    
        // Get the business ID of the first submission
        $firstSubmission = RequirementSubmission::find($request->ids[0]);
        $businessId = $firstSubmission->business_id;


        $business = Business::findOrFail($businessId);
    
        // Check if all submissions for this business are now approved
        $pendingCount = RequirementSubmission::where('business_id', $businessId)
            ->where('status', 'submitted')
            ->count();
    
        if ($pendingCount === 0) {
            // All submissions are approved, update the business status
            Business::where('id', $businessId)->update([
                'status' => 'For Inspection',
                'remarks' => 'For Inspection',
            ]);

            // 4️⃣ Find available inspector with open schedule
            $availableSchedule = InspectorSchedule::where('status', 'available')
            ->orderBy('available_date', 'asc')
            ->first();

            if ($availableSchedule) {
                // 5️⃣ Mark schedule as booked
                $availableSchedule->update(['status' => 'booked']);
                
                // 6️⃣ Create inspection record
                Inspection::create([
                    'business_id' => $businessId,
                    'user_id' => $availableSchedule->user_id,   
                    'inspector_schedule_id' => $availableSchedule->id,
                    'status' => 'Pending',
                ]);

                //insepector data
                $inspector = User::findOrFail($availableSchedule->user_id);
                $inspector_email = $inspector->email;
                $inspector_name = $inspector->first_name. ' '. $inspector->last_name;

                //building
                $building_name = $business->business_name;

                //client details
                $client = $business->user;
                $client_email = $client->email;


                $formattedDate = Carbon::parse($availableSchedule->available_date)->format('F j, Y');

                $details = [
                    'title' => 'Building Inspection Schedule',
                    'body' => 'You are assigned to inspect the '. $building_name .' this upcoming '.$formattedDate. ' please open your account to view more detaails.'
                ];

                $details2 = [
                    'title' => 'Building Inspection Schedule',
                    'body' => 'Your building will have an inspection in '.$formattedDate.' by '.$inspector_name.' please prepare your requirements that you submitted for reviewal purposes.'
                ];

                Mail::to($inspector_email)->send(new InspectionNotification($details));
                Mail::to($client_email)->send(new ClientNotification($details2));
            }
        }
    
        // return response()->json([
        //     'message' => 'All submissions approved successfully.'
        // ]);
    }

    public function approve(RequirementSubmission $submission)
    {
        DB::transaction(function () use ($submission) {

            // 1️⃣ Update this submission
            $submission->update([
                'status' => 'approved',
                'remarks' => 'Approved by admin',
            ]);
    
            // 2️⃣ Check if all submissions are approved
            $allApproved = $submission->business
                ->submissions()
                ->where('status', '!=', 'approved')
                ->doesntExist();
    
            if ($allApproved) {
                // 3️⃣ Update business status
                $submission->business->update([
                    'status' => 'For Inspection',
                    'approved_date' => now(),
                    'reamrks' => 'For Inspection'
                ]);
    
                // 4️⃣ Find available inspector with open schedule
                $availableSchedule = InspectorSchedule::where('status', 'available')
                    ->orderBy('available_date', 'asc')
                    ->first();
    
                if ($availableSchedule) {
                    // 5️⃣ Mark schedule as booked
                    $availableSchedule->update(['status' => 'booked']);
                    
                    // 6️⃣ Create inspection record
                    Inspection::create([
                        'business_id' => $submission->business_id,
                        'user_id' => $availableSchedule->user_id,   
                        'inspector_schedule_id' => $availableSchedule->id,
                        'status' => 'Pending',
                    ]);

                    //insepector data
                    $inspector = User::findOrFail($availableSchedule->user_id);
                    $inspector_email = $inspector->email;
                    $inspector_name = $inspector->first_name. ' '. $inspector->last_name;

                    //building
                    $building_name = $submission->business->business_name;

                    //client details
                    $client = $submission->business->user;
                    $client_email = $client->email;


                    $formattedDate = Carbon::parse($availableSchedule->available_date)->format('F j, Y');

                    $details = [
                        'title' => 'Building Inspection Schedule',
                        'body' => 'You are assigned to inspect the '. $building_name .' this upcoming '.$formattedDate. ' please open your account to view more detaails.'
                    ];

                    $details2 = [
                        'title' => 'Building Inspection Schedule',
                        'body' => 'Your building will have an inspection in '.$formattedDate.' by '.$inspector_name.' please prepare your requirements that you submitted for reviewal purposes.'
                    ];

                    Mail::to($inspector_email)->send(new InspectionNotification($details));
                    Mail::to($client_email)->send(new ClientNotification($details2));

                } else {
                    // No available inspector schedule
                    // (Optional) update business status differently or log the issue
                    $submission->business->update(['status' => 'Awaiting Schedule']);
                }
            }
        });
    
        return back()->with('success', 'Submission approved successfully.');
    }

    public function staffApprove(RequirementSubmission $submission){
        DB::transaction(function () use ($submission) {

            // 1️⃣ Update this submission
            $submission->update([
                'status' => 'approved',
                'remarks' => 'Approved by staff',
            ]);
    
            // 2️⃣ Check if all submissions are approved
            $allApproved = $submission->business
                ->submissions()
                ->where('status', '!=', 'approved')
                ->doesntExist();
    
            if ($allApproved) {
                // 3️⃣ Update business status
                $submission->business->update([
                    'status' => 'For Inspection',
                    'approved_date' => now(),
                ]);
    
                // 4️⃣ Find available inspector with open schedule
                $availableSchedule = InspectorSchedule::where('status', 'available')
                    ->orderBy('available_date', 'asc')
                    ->first();
    
                if ($availableSchedule) {
                    // 5️⃣ Mark schedule as booked
                    $availableSchedule->update(['status' => 'booked']);
                    
                    // 6️⃣ Create inspection record
                    Inspection::create([
                        'business_id' => $submission->business_id,
                        'user_id' => $availableSchedule->user_id,   
                        'inspector_schedule_id' => $availableSchedule->id,
                        'status' => 'Pending',
                    ]);

                    //insepector data
                    $inspector = User::findOrFail($availableSchedule->user_id);
                    $inspector_email = $inspector->email;
                    $inspector_name = $inspector->first_name. ' '. $inspector->last_name;

                    //building
                    $building_name = $submission->business->business_name;

                    //client details
                    $client = $submission->business->user;
                    $client_email = $client->email;


                    $formattedDate = Carbon::parse($availableSchedule->available_date)->format('F j, Y');

                    $details = [
                        'title' => 'Building Inspection Schedule',
                        'body' => 'You are assigned to inspect the '. $building_name .' this upcoming '.$formattedDate. ' please open your account to view more detaails.'
                    ];

                    $details2 = [
                        'title' => 'Building Inspection Schedule',
                        'body' => 'Your building will have an inspection in '.$formattedDate.' by '.$inspector_name.' please prepare your requirements that you submitted for reviewal purposes.'
                    ];

                    Mail::to($inspector_email)->send(new InspectionNotification($details));
                    Mail::to($client_email)->send(new ClientNotification($details2));

                } else {
                    // No available inspector schedule
                    // (Optional) update business status differently or log the issue
                    $submission->business->update(['status' => 'Awaiting Schedule']);
                }
            }
        });
    
        return back()->with('success', 'Submission approved successfully.');
    }

    public function reject(RequirementSubmission $submission, Request $request)
    {
        $business = $submission->business;
        $user = $business->user;
        $client_email = $user->email;

        $requirement = $submission->requirement;
        $requirement_name = $requirement->title;
  
        $submission->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks', 'Rejected by admin'),
        ]);

        // $submission->business->update(['status' => 'Rejected']);

        $details = [
            'title' => 'Client Requirement Submission Notification',
            'body' => 'Your requirement submission of '. $requirement_name . ' has been rejected please submit a correct one.'
        ];

        Mail::to($client_email)->send(new SubmissionNotification($details));

        return back()->with('success', 'Submission rejected successfully.');
    }

    public function staffReject(RequirementSubmission $submission, Request $request){
        $business = $submission->business;
        $user = $business->user;
        $client_email = $user->email;

        $requirement = $submission->requirement;
        $requirement_name = $requirement->title;
  
        $submission->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks', 'Rejected by staff'),
        ]);

        // $submission->business->update(['status' => 'Rejected']);

        $details = [
            'title' => 'Client Requirement Submission Notification',
            'body' => 'Your requirement submission of '. $requirement_name . ' has been rejected please submit a correct one.'
        ];

        Mail::to($client_email)->send(new SubmissionNotification($details));

        return back()->with('success', 'Submission rejected successfully.');
    }
}
