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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Government ID", "Birth Certificate"
            $table->string('slug')->unique(); // e.g., "government_id", "birth_certificate"
            $table->text('description')->nullable(); // Description of what this document is for
            $table->boolean('is_required')->default(true); // Whether this document is mandatory
            $table->json('accepted_formats')->nullable(); // e.g., ["pdf", "jpg", "png"]
            $table->integer('max_file_size')->default(5120); // Max file size in KB (default 5MB)
            $table->text('instructions')->nullable(); // Instructions for uploading this document
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering in forms
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
