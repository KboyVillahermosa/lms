<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert any string-encoded JSON values into proper JSON arrays
        $rows = DB::table('document_types')->get();

        foreach ($rows as $row) {
            $val = $row->accepted_formats;

            if (is_null($val)) {
                continue;
            }

            // If the value is already an array (JSON column cast will return string here), skip
            // We'll attempt to decode if it's a string containing JSON
            if (is_string($val)) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    DB::table('document_types')->where('id', $row->id)->update([
                        'accepted_formats' => json_encode(array_map('strtolower', $decoded)),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Try to interpret comma separated values
                    $parts = preg_split('/\s*,\s*/', $val);
                    if (count($parts) > 1) {
                        $parts = array_map('strtolower', $parts);
                        DB::table('document_types')->where('id', $row->id)->update([
                            'accepted_formats' => json_encode($parts),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse safely
    }
};
