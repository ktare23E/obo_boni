<?php

use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\BuildingApplicationController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientOTPController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\InspectorProfileController;
use App\Http\Controllers\InspectorScheduleController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\RequirementSampleController;
use App\Http\Controllers\RequirementSubmissionController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\RequirementSubmission;
use Faker\Guesser\Name;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');


Route::get('/client_otp/{user}', [ClientOTPController::class, 'otpPage'])->name('client_otp');
Route::get('/track_application', [WelcomeController::class, 'trackApplication'])->name('track_application');

Route::post('/track-application/check', [BuildingApplicationController::class, 'checkReference'])
    ->name('track.application.check');

Route::get('/track-application/{reference_number}', [BuildingApplicationController::class, 'showTimeline'])
    ->name('track.application.status');


Route::post('/resend-otp', [ClientOTPController::class, 'resend'])->name('resend_otp');
Route::post('/otp-login', [ClientOTPController::class, 'verify'])->name('otp_login');

Route::get('/reports/generate', [ReportController::class, 'generate'])
    ->name('reports.generate')
    ->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class]);

Route::get('/myadmin',function(){
    return redirect('/phpmyadmin/index.php');
    
});

Route::get('/new_report/{permit}', [BuildingApplicationController::class, 'report'])->name('new_report');


Route::get('/submissions/file/{submission}', [RequirementSubmissionController::class, 'view'])->name('submissions.file');

Route::post('/submissions/approve-all', [RequirementSubmissionController::class, 'approveAll'])->name('submissions.approveAll');


// Route::get('/api/address-search', [AddressController::class, 'search']);


Route::post('/admin/permits/{permit}/release', [PermitController::class, 'release'])->name('permits.release');
Route::post('/permits', [PermitController::class, 'store'])->name('permits.store');


Route::middleware('auth')->group(function(){
    Route::middleware(['userType:Admin'])->group(function(){
        Route::get('/admin_dashboard',[DashboardController::class,'admin'])->name('admin_dashboard');
        
        Route::get('/users',[UserController::class,'index'])->name('users');
        Route::get('/create_user',[UserController::class,'create'])->name('create_user');
        Route::get('/edit_user/{user}',[UserController::class,'edit'])->name('edit_user');
        Route::post('/store_user',[UserController::class,'store'])->name('store_user');
        Route::put('/update_user',[UserController::class,'update'])->name('update_user');
        
        Route::get('/inspector',[InspectorController::class,'index'])->name('inspector');
        Route::get('/admin_view_inspector_schedule/{user}',[InspectorController::class,'viewSchedule'])->name('admin_view_inspector_schedule');
        Route::get('/create_inspector',[InspectorController::class,'create'])->name('create_inspector');
        Route::get('/edit_inspector/{user}',[InspectorController::class,'edit'])->name('edit_inspector');
        
        Route::get('/clients',[UserController::class,'clients'])->name('clients');

        Route::get('/admin_profile',[AdminProfileController::class,'profile'])->name('admin_profile');
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.password.update');

        
        Route::get('/requirements',[RequirementController::class,'index'])->name('requirements');
        Route::get('/create_requirements',[RequirementController::class,'create'])->name('create_requirements');
        Route::post('/store_requirement',[RequirementController::class,'store'])->name('store_requirement');
        Route::get('/edit_requirement/{requirement}',[RequirementController::class,'edit'])->name('edit_requirement');
        Route::put('/update_requirement/{id}', [RequirementController::class, 'update'])->name('update_requirement');
        Route::post('/requirements/sample/{id}/update', [RequirementSampleController::class, 'update'])->name('update_requirement_sample');
        
        Route::get('/admin_inspection',[InspectionController::class,'adminInspection'])->name('admin_inspection');

        
        Route::get('/submissions',[RequirementSubmissionController::class,'index'])->name('submissions');
        Route::get('/requirement_submissions/{business}', [RequirementSubmissionController::class, 'show'])->name('requirement_submissions.show');
        Route::post('/submissions/{submission}/approve', [RequirementSubmissionController::class, 'approve'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [RequirementSubmissionController::class, 'reject'])->name('submissions.reject');

        
        Route::get('/business_list',[BusinessController::class,'index'])->name('business_list');

        Route::get('/permit_list',[PermitController::class,'index'])->name('permit_list');

        
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports');

    });

    Route::middleware(['userType:Inspector'])->group(function(){
        Route::get('/inspector_dashboard',[DashboardController::class,'inspector'])->name('inspector_dashboard');

        Route::get('/inspector_profile',[InspectorProfileController::class,'profile'])->name('inspector_profile');
        Route::put('/inspector_profile/update', [InspectorProfileController::class, 'update'])->name('update_inspector_profile');
        Route::put('/inspector_profile/password', [InspectorProfileController::class, 'updatePassword'])->name('inspector.password.update');

        Route::get('/inspector_schedule', [InspectorScheduleController::class, 'index'])->name('inspector_schedule');
        Route::post('/inspector/schedule/store', [InspectorScheduleController::class, 'store'])->name('inspector.schedule.store');
        Route::delete('/inspector/schedule/{id}', [InspectorScheduleController::class, 'destroy'])->name('inspector.schedule.destroy');

        Route::get('/assigned_inpsection', [InspectionController::class, 'index'])->name('assigned_inpsection');
        Route::get('/ins_requirement_submissions/{business}', [RequirementSubmissionController::class, 'showInspector'])->name('ins_requirement_submissions.show');

        Route::post('/admin/businesses/{business}/approve', [InspectionController::class, 'approve'])
            ->name('business.approve');

        Route::post('/admin/businesses/{business}/reject', [InspectionController::class, 'reject'])
            ->name('business.reject');

    });

    Route::middleware(['userType:Staff'])->group(function(){
        Route::get('/staff_dashboard',[DashboardController::class,'staff'])->name('staff_dashboard');
        Route::get('/staff_submissions',[RequirementSubmissionController::class,'staffSubmissions'])->name('staff_submissions');

        Route::get('/staff_profile',[StaffProfileController::class,'profile'])->name('staff_profile');
        Route::put('/staff_profile/update', [StaffProfileController::class, 'update'])->name('staff.profile.update');
        Route::put('/staff_profile/password', [StaffProfileController::class, 'updatePassword'])->name('staff.password.update');


        Route::get('/staff_requirement_submissions/{business}', [RequirementSubmissionController::class, 'showStaff'])->name('staff_requirement_submissions');
        Route::post('/staff_submissions/{submission}/approve', [RequirementSubmissionController::class, 'staffApprove'])->name('staff_approve_submissions');
        Route::post('/staff_submissions/{submission}/reject', [RequirementSubmissionController::class, 'staffReject'])->name('staff_reject_submissions');

        Route::get('/staff_inspection',[InspectionController::class,'staffInspection'])->name('staff_inspection');

        Route::get('/staff_inspector',[InspectorController::class,'staffInspector'])->name('staff_inspector');

        Route::get('/staff_clients',[UserController::class,'staffClients'])->name('staff_clients');

        Route::get('/view_inspector_schedule/{user}',[InspectorController::class,'inspectorSchedule'])->name('view_inspector_schedule');

        Route::get('/staff_business_list',[BusinessController::class,'staffList'])->name('staff_business_list');
        Route::get('/staff_permit_list',[PermitController::class,'staffList'])->name('staff_permit_list');
    });


    Route::middleware(['userType:Client'])->group(function(){
        Route::get('/client_dashboard', [ClientController::class, 'index'])->name('client_dashboard');
        Route::get('/buildings', [ClientController::class, 'building'])->name('buildings.index');
        Route::get('/create_building', [ClientController::class, 'createBuilding'])->name('create_building');
        Route::get('/show_building/{business}', [ClientController::class, 'show'])->name('show_building');
        Route::post('/store_building', [BusinessController::class, 'store'])->name('store_building');

        Route::post('/requirements/{submission}/reupload', [BusinessController::class, 'reupload'])
            ->name('requirements.reupload');

        Route::get('/profile', [ClientController::class, 'profile'])->name('profile');

        Route::put('/client/profile', [ClientController::class, 'update'])->name('client.profile.update');
        Route::put('/client/password', [ClientController::class, 'updatePassword'])->name('client.password.update');
    });
});







// Route::get('/inspector_sche',[UserController::class,'index'])->name('inspector');



// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
