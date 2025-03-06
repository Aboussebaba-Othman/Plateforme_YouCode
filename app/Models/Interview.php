<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Interview extends Model
{
    use HasFactory;

    // Interview types
    const TYPE_TECHNICAL = 'technical';
    const TYPE_ADMINISTRATIVE = 'administrative';
    const TYPE_CME = 'CME';
    
    // Interview statuses
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'candidate_id',
        'staff_id',
        'interview_date',
        'interview_time',
        'interview_type',
        'location',
        'status',
        'notes',
        'invitation_sent_at',
        'cme_group_id'
    ];

    protected $casts = [
        'interview_date' => 'date',
        'invitation_sent_at' => 'datetime',
    ];

    /**
     * Get the candidate associated with this interview.
     */
    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    /**
     * Get the staff member associated with this interview.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    /**
     * Get the CME group this interview belongs to (if CME type).
     */
    public function cmeGroup()
    {
        return $this->belongsTo(CmeGroup::class, 'cme_group_id');
    }
    
    
    /**
     * Get the feedback for this interview.
     */
    public function feedbacks()
    {
        return $this->hasMany(InterviewFeedback::class);
    }

    /**
     * Scope a query to only include upcoming interviews.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('interview_date', '>=', Carbon::today())
            ->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include past interviews.
     */
    public function scopePast($query)
    {
        return $query->where(function($q) {
            $q->where('interview_date', '<', Carbon::today())
                ->orWhere('status', self::STATUS_COMPLETED)
                ->orWhere('status', self::STATUS_CANCELLED)
                ->orWhere('status', self::STATUS_NO_SHOW);
        });
    }
    
    /**
     * Scope a query to only include interviews by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('interview_type', $type);
    }

    /**
     * Get the formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->interview_date->format('d/m/Y');
    }

    /**
     * Get the formatted time.
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->interview_time)->format('H:i');
    }

    /**
     * Get the interview type label.
     */
    public function getTypeLabelAttribute()
    {
        switch ($this->interview_type) {
            case self::TYPE_TECHNICAL:
                return 'Technique';
            case self::TYPE_ADMINISTRATIVE:
                return 'Administratif';
            case self::TYPE_CME:
                return 'CME';
            default:
                return ucfirst($this->interview_type);
        }
    }

    /**
     * Get the status label with appropriate styling.
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_SCHEDULED:
                return '<span class="badge bg-primary">Programmé</span>';
            case self::STATUS_COMPLETED:
                return '<span class="badge bg-success">Terminé</span>';
            case self::STATUS_CANCELLED:
                return '<span class="badge bg-danger">Annulé</span>';
            case self::STATUS_NO_SHOW:
                return '<span class="badge bg-warning">Absence</span>';
            default:
                return '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
        }
    }
    
    /**
     * Check if this interview has any feedback.
     */
    public function getHasFeedbackAttribute()
    {
        return $this->feedbacks()->exists();
    }
    
    /**
     * Get the average rating from all feedbacks.
     */
    public function getAverageRatingAttribute()
    {
        if (!$this->has_feedback) {
            return null;
        }
        
        return round($this->feedbacks()->avg('rating'), 1);
    }
}