<?php

use App\Models\User;
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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('address');
            $table->string('type_of_business');
            $table->string('building_type');
            $table->string('registration_no')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['Pending', 'Under Review of Admin', 'For Inspection','Approved', 'Rejected','For Creatig Permit','For Permit Release','Permit Released'])->default('Pending');
            $table->date('approved_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};

