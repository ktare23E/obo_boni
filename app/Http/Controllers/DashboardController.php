<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Inspection;
use App\Models\Permit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function admin(){
        $pendingInspectionCount = Inspection::where('status','Pending')
                                    ->count();
        $releasedPermitCount = Permit::where('status','released')
                                    ->count();
        $approvedPermitCount = Business::where('status','Approved')
        ->count();
        $totalCount = Business::count();
        $recentBusiness = Business::with('user')
                            ->latest()
                            ->take(4)
                            ->get();
        // return $recentBusiness;

        return Inertia::render('Admin/Dashboard',[
            'pendingInspectionCount' => $pendingInspectionCount,
            'releasedPermitCount' => $releasedPermitCount,
            'approvedPermitCount' => $approvedPermitCount,
            'totalCount' => $totalCount,
            'recentBusiness' => $recentBusiness
        ]);
    }

    public function inspector(){
        $pendingInspectionCount = Inspection::where('status','Pending')
                            ->count();
        $releasedPermitCount = Permit::where('status','released')
                                    ->count();
        $approvedPermitCount = Business::where('status','Approved')
                                ->count();
        $totalCount = Business::count();
        $recentBusiness = Business::with('user')
                    ->latest()
                    ->take(4)
                    ->get();
        return Inertia::render('Inspector/Dashboard',[
            'pendingInspectionCount' => $pendingInspectionCount,
            'releasedPermitCount' => $releasedPermitCount,
            'approvedPermitCount' => $approvedPermitCount,
            'totalCount' => $totalCount,
            'recentBusiness' => $recentBusiness
        ]);
    }
    
    public function staff(){
        $pendingInspectionCount = Inspection::where('status','Pending')
        ->count();
        $releasedPermitCount = Permit::where('status','released')
                ->count();
        $approvedPermitCount = Business::where('status','Approved')
        ->count();
        $totalCount = Business::count();
        $recentBusiness = Business::with('user')
        ->latest()
        ->take(4)
        ->get();
        return Inertia::render('Staff/Dashboard',[
            'pendingInspectionCount' => $pendingInspectionCount,
            'releasedPermitCount' => $releasedPermitCount,
            'approvedPermitCount' => $approvedPermitCount,
            'totalCount' => $totalCount,
            'recentBusiness' => $recentBusiness
        ]);
    }

    public function client(){
        return Inertia::render('Welcome');
    }
}
