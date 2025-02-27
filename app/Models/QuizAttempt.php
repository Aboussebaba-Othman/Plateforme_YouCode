<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_id',
        'quiz_id',
        'started_at',
        'completed_at',
        'score',
        'status',
        'answers',
        'current_question',
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function isTimedOut()
    {
        if (!$this->completed_at && $this->started_at) {
            $timeLimit = $this->quiz->time_limit * 60; 
            return now()->diffInSeconds($this->started_at) > $timeLimit;
        }
        
        return false;
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->completed_at) {
            return 0;
        }
        
        $timeLimit = $this->quiz->time_limit * 60; 
        $elapsed = now()->diffInSeconds($this->started_at);
        $remaining = $timeLimit - $elapsed;
        
        return max(0, $remaining);
    }
    
}