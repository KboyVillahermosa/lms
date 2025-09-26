<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'course_id', 'instructions', 'total_points', 'due_at'
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(AssignmentQuestion::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(\App\Models\AssignmentSubmission::class);
    }

    public function isVisibleTo(User $user = null)
    {
        // if no specific students are assigned, visible to everyone
        if ($this->students()->count() === 0) {
            return true;
        }

        if (! $user) return false;

        return $this->students()->where('user_id', $user->id)->exists();
    }
}
