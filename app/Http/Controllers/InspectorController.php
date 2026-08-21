<?php

namespace App\Http\Controllers;

use App\Models\InspectorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InspectorController extends Controller
{
    public function index()
    {
        $inspector = User::where('user_type', 'Inspector')->get();
        // return $inspector;

        return Inertia::render('Admin/Inspector/Index', [
            'inspector' => $inspector
        ]);
    }

    public function staffInspector()
    {
        $inspector = User::where('user_type', 'Inspector')->get();
        // return $inspector;

        return Inertia::render('Staff/Inspector/Index', [
            'inspector' => $inspector
        ]);
    }


    public function create()
    {
        return Inertia::render('Admin/Inspector/Create');
    }

    public function edit(User $user)
    {

        return Inertia::render('Admin/Inspector/Edit', [
            'user' => $user
        ]);
    }

    public function inspectorSchedule(User $user)
    {
        $schedules = InspectorSchedule::with('inspection.business.user')
        ->where('user_id', $user->id)
        ->get();

        $events = $schedules->map(function($schedule){
            return [
                'id' => $schedule->id,
                'title' => $schedule->status == 'booked'
                    ? $schedule->inspection?->business->business_name 
                    : ucfirst($schedule->status),

                'start' => $schedule->available_date,
                'color' => $schedule->status == 'available' ? '#22c55e' : '#f87171',

                'extendedProps' => [
                    'status' => $schedule->status,
                    'businessName' => $schedule->inspection?->business->business_name,
                    'businessAddress' => $schedule->inspection?->business->address,
                    'inspectionStatus' => $schedule->inspection?->status,
                ]
            ];
        });

        // ✅ NEW FILTER LOGIC
        $filteredEvents = $events->filter(function ($event) {
            $props = $event['extendedProps'];

            // ALWAYS include available schedules
            if ($props['status'] === 'available') {
                return true;
            }

            // For booked schedules: include only if not null
            return !is_null($props['businessName']) ||
                !is_null($props['businessAddress']) ||
                !is_null($props['inspectionStatus']);
        })->values();

        return Inertia::render('Staff/Inspector/Schedules', [
            'events' => $filteredEvents,
            'user' => $user
        ]);
    }

    public function viewSchedule(User $user)
    {
        $schedules = InspectorSchedule::with('inspection.business.user')
            ->where('user_id', $user->id)
            ->get();
    
        $events = $schedules->map(function($schedule){
            return [
                'id' => $schedule->id,
                'title' => $schedule->status == 'booked'
                    ? $schedule->inspection?->business->business_name 
                    : ucfirst($schedule->status),
    
                'start' => $schedule->available_date,
                'color' => $schedule->status == 'available' ? '#22c55e' : '#f87171',
    
                'extendedProps' => [
                    'status' => $schedule->status,
                    'businessName' => $schedule->inspection?->business->business_name,
                    'businessAddress' => $schedule->inspection?->business->address,
                    'inspectionStatus' => $schedule->inspection?->status,
                ]
            ];
        });
    
        // ✅ NEW FILTER LOGIC
        $filteredEvents = $events->filter(function ($event) {
            $props = $event['extendedProps'];
    
            // ALWAYS include available schedules
            if ($props['status'] === 'available') {
                return true;
            }
    
            // For booked schedules: include only if not null
            return !is_null($props['businessName']) ||
                   !is_null($props['businessAddress']) ||
                   !is_null($props['inspectionStatus']);
        })->values();
    
        return Inertia::render('Admin/Inspector/Schedules', [
            'events' => $filteredEvents,
            'user' => $user
        ]);
    }
    
    
}
