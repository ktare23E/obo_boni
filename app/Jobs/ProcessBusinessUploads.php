<?php

namespace App\Jobs;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class ProcessBusinessUploads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $businessId;
    public $files;

    /**
     * Create a new job instance.
     */
    public function __construct($businessId,$files)
    {
        $this->businessId = $businessId;
        $this->files = $files;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $business = Business::find($this->businessId);

        foreach ($this->files as $file) {

            // Move from public temp → private disk
            $finalPath = Storage::disk('public')->putFile(
                'requirement_submissions',
                new \Illuminate\Http\File(storage_path("app/public/" . $file['temp_path']))
            );
            
            // Save DB record
            $business->submissions()->create([
                'requirement_id' => $file['requirement_id'],
                'file_path'      => $finalPath,
                'status'         => 'submitted',
            ]);

            // Delete temp file
            Storage::disk('public')->delete($file['temp_path']);
        }
    }
}
