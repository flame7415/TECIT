<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangay_admin_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('position'); // e.g., barangay secretary
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            $table->string('official_email')->unique();
            $table->string('contact_number', 20);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_admin_requests');
    }
};

