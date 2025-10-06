<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_required',
        'accepted_formats',
        'max_file_size',
        'instructions',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'accepted_formats' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active document types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for required document types
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for optional document types
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Get documents for this type
     */
    public function enrollmentDocuments()
    {
        return $this->hasMany(EnrollmentDocument::class);
    }

    /**
     * Get max file size in MB
     */
    public function getMaxFileSizeMbAttribute()
    {
        return round($this->max_file_size / 1024, 1);
    }

    /**
     * Get accepted formats as string
     */
    public function getAcceptedFormatsStringAttribute()
    {
        return implode(', ', $this->accepted_formats ?? []);
    }
}
