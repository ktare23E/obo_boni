<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function trackApplication(){
        // return Business::all();
        return Inertia::render('TrackApplication');
    }
}
