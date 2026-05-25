<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_id')->unique(); // Format: COMP-YYYYMMDD-XXXX
            $table->unsignedBigInteger('user_id'); // Resident who filed
            $table->unsignedBigInteger('barangay_id');
            $table->unsignedBigInteger('category_id');
            $table->text('description');
            $table->string('vulnerability_flag')->nullable(); // elderly, PWD, pregnant, null
            $table->decimal('priority_score', 5, 2)->default(0); // Calculated priority score
            $table->string('priority_level')->default('low'); // low, medium, high, critical
            $table->string('status')->default('pending'); // pending, acknowledged, in_progress, resolved, closed
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('escalated_to_municipal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

