<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Government-Issued ID',
                'slug' => 'government_id',
                'description' => 'Valid government-issued photo identification (Driver\'s License, Passport, National ID)',
                'is_required' => true,
                'accepted_formats' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'max_file_size' => 5120, // 5MB
                'instructions' => 'Upload a clear, colored scan or photo of your government-issued ID. Both front and back sides must be visible.',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Birth Certificate',
                'slug' => 'birth_certificate',
                'description' => 'Official birth certificate for age verification',
                'is_required' => true,
                'accepted_formats' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'max_file_size' => 5120,
                'instructions' => 'Upload a clear scan of your official birth certificate issued by the government.',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Academic Transcript',
                'slug' => 'academic_transcript',
                'description' => 'Official transcript from previous educational institution',
                'is_required' => true,
                'accepted_formats' => json_encode(['pdf']),
                'max_file_size' => 10240, // 10MB
                'instructions' => 'Upload official transcripts from your most recent educational institution. Documents must be in PDF format.',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Proof of Address',
                'slug' => 'proof_of_address',
                'description' => 'Recent utility bill, bank statement, or government mail showing your current address',
                'is_required' => true,
                'accepted_formats' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'max_file_size' => 5120,
                'instructions' => 'Upload a recent document (within last 3 months) showing your current address.',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Medical Certificate',
                'slug' => 'medical_certificate',
                'description' => 'Medical fitness certificate from a licensed physician',
                'is_required' => false,
                'accepted_formats' => json_encode(['pdf', 'jpg', 'jpeg', 'png']),
                'max_file_size' => 5120,
                'instructions' => 'Upload a medical certificate if required by your chosen program.',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'English Proficiency Certificate',
                'slug' => 'english_proficiency',
                'description' => 'TOEFL, IELTS, or equivalent English proficiency test results',
                'is_required' => false,
                'accepted_formats' => json_encode(['pdf']),
                'max_file_size' => 5120,
                'instructions' => 'Upload English proficiency test results if you are a non-native English speaker.',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('document_types')->insert($documentTypes);
    }
}
