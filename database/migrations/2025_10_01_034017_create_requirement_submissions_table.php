<?php

use App\Models\Business;
use App\Models\Requirement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requirement_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Requirement::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Business::class)->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->enum('status', ['submitted', 'approved', 'rejected', 'resubmitted'])->default('submitted');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_submissions');
    }
};
