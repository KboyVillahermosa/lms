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
            // Drop foreign key if exists then modify column to nullable
            if (Schema::hasColumn('enrollment_requests', 'course_id')) {
                // Attempt to drop foreign key by convention name
                try {
                    $table->dropForeign(['course_id']);
                } catch (\Exception $e) {
                    // ignore if FK doesn't exist by that name
                }

                $table->unsignedBigInteger('course_id')->nullable()->change();

                // Re-add foreign key with set null on delete
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('enrollment_requests', 'course_id')) {
                try {
                    $table->dropForeign(['course_id']);
                } catch (\Exception $e) {
                    // ignore
                }

                $table->unsignedBigInteger('course_id')->nullable(false)->change();
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            }
        });
    }
};
