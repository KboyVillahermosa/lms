<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'question', 'points', 'attachment'
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function attachmentUrl()
    {
        if (! $this->attachment) return null;
        return asset('storage/' . $this->attachment);
    }
}
