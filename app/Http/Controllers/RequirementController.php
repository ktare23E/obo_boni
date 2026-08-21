<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function index(){
        $requirements = Requirement::with('samples')->get();

        return Inertia::render('Admin/Requirements/Index',[
            'requirements' => $requirements
        ]);
    }

    public function create(){
        return Inertia::render('Admin/Requirements/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_type_allowed' => 'nullable|string',
            'requirement_type' => 'required|string',
    
            'requirement_sample' => 'nullable|array',
            'requirement_sample.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);
    
        // Create Requirement first
        $requirement = Requirement::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_type_allowed' => $data['file_type_allowed'] ?? null,
            'requirement_type' => $data['requirement_type'],
        ]);
    
        // Save each uploaded file
        if ($request->hasFile('requirement_sample')) {
            foreach ($request->file('requirement_sample') as $file) {
                $path = $file->store('requirements_samples', 'public');
    
                $requirement->samples()->create([
                    'requirement_sample' => $path
                ]);
            }
        }
    
        return back()->with('success', 'Requirement created successfully!');
    }
    
    

    public function edit(Requirement $requirement){

        return Inertia::render('Admin/Requirements/Edit',[
            'requirement' => $requirement
        ]);
    }

    public function update(Request $request, $id)
    {

        $requirement = Requirement::findOrFail($id);
    
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file_type_allowed' => 'required|string',
            'requirement_type' => 'required|string',
        ]);
    
        $requirement->update($data);
    
        return back()->with('success', 'Requirement updated successfully!');
    }
    
    public function updateSample(Request $request, $id)
    {

        $requirement = Requirement::findOrFail($id);

        $request->validate([
            'requirement_sample' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('requirement_sample')) {
            // Delete old file if exists
            if ($requirement->requirement_sample && Storage::disk('public')->exists($requirement->requirement_sample)) {
                Storage::disk('public')->delete($requirement->requirement_sample);
            }
        
            // Store new file on public disk
            $requirement->requirement_sample = $request->file('requirement_sample')->store('requirements_samples', 'public');
            $requirement->save();
        }

        // return response()->json(['message' => 'Requirement sample updated successfully.']);
    }

    
    
}
