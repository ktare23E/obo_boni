<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\InspectorSchedule;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class InspectorScheduleController extends Controller
{

    public function index()
{
    $user = Auth::user();
    $schedules = InspectorSchedule::
                    with('inspection.business.user')
                    ->where('user_id',$user->id)->get();

    // return $schedules;
    
    // return $schedules;
    $events = $schedules->map(function($schedule){
        return [
            'id' => $schedule->id,
            'title' => $schedule->status == 'booked'
                ? $schedule->inspection?->business->business_name 
                : ucfirst($schedule->status),

            'start' => $schedule->available_date,
            'color' => $schedule->status == 'available' ? '#22c55e' : '#f87171',

            // Extra data for the calendar
            'extendedProps' => [
                'status' => $schedule->status,
                'businessName' => $schedule->inspection?->business->business_name ?? null,
                'businessAddress' => $schedule->inspection?->business->address ?? null,
                'inspectionStatus' => $schedule->inspection?->status ?? null,
            ]
        ];
    });

    // return $events;

    return Inertia::render('Inspector/Schedule/Index',[
        'events' => $events
    ]);
}

public function store(Request $request)
{
    // return $request->all();

    $request->validate([
        'date' => 'required|date|after_or_equal:today'
    ]);

    $schedule = InspectorSchedule::firstOrCreate([
        'user_id' => Auth::id(),
        'available_date' => $request->date,
    ], [
        'status' => 'available'
    ]);

    return response()->json([
        'success' => true,
        'event' => [
            'id' => $schedule->id,
            'title' => ucfirst($schedule->status),
            'start' => $schedule->available_date,
            'color' => '#22c55e',
        ]
    ]);
}

public function destroy($id)
{
    $schedule = InspectorSchedule::where('user_id', Auth::id())->findOrFail($id);

    // Check if this schedule has a booked inspection
    $inspection = Inspection::where('inspector_schedule_id', $schedule->id)->first();

    if ($inspection) {
        // Find next nearest available date
        $newSchedule = InspectorSchedule::where('user_id', Auth::id())
            ->where('status', 'available')
            ->where('available_date', '>', $schedule->available_date)
            ->orderBy('available_date', 'asc')
            ->first();

        if (!$newSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'No available future dates to reassign the booking.'
            ], 400);
        }

        // Reassign inspection to the new available schedule
        $inspection->inspector_schedule_id = $newSchedule->id;
        $inspection->save();

        // Mark new schedule as booked
        $newSchedule->status = 'booked';
        $newSchedule->save();
    }

    // Finally delete original schedule
    $schedule->delete();

    return response()->json(['success' => true]);
}


}
