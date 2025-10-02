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
        Schema::create('enrollment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->enum('status', ['pending', 'approved', 'rejected', 'resubmission_required'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin feedback
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Admin who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Ensure one document per type per enrollment request
            $table->unique(['enrollment_request_id', 'document_type_id'], 'enrollment_docs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_documents');
    }
};
