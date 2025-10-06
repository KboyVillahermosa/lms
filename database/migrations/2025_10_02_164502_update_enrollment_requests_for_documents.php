<?php

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
        Schema::table('enrollment_requests', function (Blueprint $table) {
            // Add new status options for document verification
            $table->enum('document_status', [
                'not_submitted', 
                'submitted', 
                'under_review', 
                'approved', 
                'rejected', 
                'resubmission_required'
            ])->default('not_submitted')->after('status');
            
            // Add personal information fields
            $table->string('phone')->nullable()->after('reason');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            
            // Track enrollment progress
            $table->json('completed_steps')->nullable(); // Track which steps are completed
            $table->timestamp('documents_submitted_at')->nullable();
            $table->timestamp('documents_reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_requests', function (Blueprint $table) {
            $table->dropColumn([
                'document_status',
                'phone',
                'address', 
                'date_of_birth',
                'gender',
                'completed_steps',
                'documents_submitted_at',
                'documents_reviewed_at'
            ]);
        });
    }
};
