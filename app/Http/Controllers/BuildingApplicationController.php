<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Permit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BuildingApplicationController extends Controller
{
    public function checkReference(Request $request)
    {
        $request->validate([
            'registration_no' => 'required|string',
        ]);
    
        $registration_no = trim($request->registration_no);
    
        $application = Business::where('registration_no', $registration_no)->first();
    
        return response()->json([
            'application' => $application,
        ]);
    }
    


    // Timeline page
    public function showTimeline($registration_no)
    {
        // return 'naa ko timeline';
        $application = Business::where('registration_no', $registration_no)->firstOrFail();

        return Inertia::render('TrackPage',[
            'application' => $application
        ]);
    }

    public function report(Permit $permit){
        // return $permit;
        $permit->update([
            'status' => 'released',
            'release_date' => Carbon::now(),
        ]);

        $building = $permit->business;

        $building->update([
            'status' => 'Permit Released',
            'remarks' => 'Permit Released'
        ]);
        return Inertia::render('TestReport', [
            'permit' => $permit->load('business.user')
        ]);
    }
    
}
