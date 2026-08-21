<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequirementSample;
use Illuminate\Support\Facades\Storage;

class RequirementSampleController extends Controller
{
    public function update(Request $request, $id)
    {
        $sample = RequirementSample::findOrFail($id);

        $allowedType = strtolower($sample->file_type_allowed ?? $sample->requirement->file_type_allowed);

        $mimes = match ($allowedType) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image' => 'image/jpeg,image/png,image/jpg',
            default => '*/*',
        };

        $request->validate([
            'requirement_sample' => ['required', 'file', "mimetypes:$mimes"],
        ]);

        // Delete old file
        if ($sample->requirement_sample && Storage::disk('public')->exists($sample->requirement_sample)) {
            Storage::disk('public')->delete($sample->requirement_sample);
        }

        // Store new file
        $path = $request->file('requirement_sample')->store('requirements_samples', 'public');

        // Update database
        $sample->requirement_sample = $path;
        $sample->save();

        return back()->with('success', 'Requirement sample re-uploaded successfully!');
    }

}
