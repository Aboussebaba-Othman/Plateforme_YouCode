<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'staff_id',
        'comments',
        'rating',
        'recommendation'
    ];

    // Relationship to interview
    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    // Relationship to staff member
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}