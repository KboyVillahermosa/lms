<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'course_id', 
        'status',
        'admin_id',
        'reason',
        'admin_notes',
        'processed_at',
        'document_status',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'completed_steps',
        'documents_submitted_at',
        'documents_reviewed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'date_of_birth' => 'date',
        'completed_steps' => 'array',
        'documents_submitted_at' => 'datetime',
        'documents_reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get enrollment documents
     */
    public function documents()
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    /**
     * Check if all required documents are submitted
     */
    public function hasAllRequiredDocuments()
    {
        $requiredDocuments = DocumentType::active()->required()->count();
        $submittedDocuments = $this->documents()->count();
        
        return $submittedDocuments >= $requiredDocuments;
    }

    /**
     * Check if all documents are approved
     */
    public function allDocumentsApproved()
    {
        if (!$this->hasAllRequiredDocuments()) {
            return false;
        }

        return $this->documents()->where('status', '!=', 'approved')->count() === 0;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $steps = ['personal_info', 'documents', 'course_selection', 'review'];
        $completed = $this->completed_steps ?? [];
        
        return round((count($completed) / count($steps)) * 100);
    }

    /**
     * Check if step is completed
     */
    public function isStepCompleted($step)
    {
        return in_array($step, $this->completed_steps ?? []);
    }

    /**
     * Mark step as completed
     */
    public function markStepCompleted($step)
    {
        $completed = $this->completed_steps ?? [];
        if (!in_array($step, $completed)) {
            $completed[] = $step;
            $this->update(['completed_steps' => $completed]);
        }
    }
}
