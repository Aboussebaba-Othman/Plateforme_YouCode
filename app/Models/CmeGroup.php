<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CmeGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_date',
        'session_time',
        'staff_id'
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    
    public function interviews()
    {
        return $this->hasMany(Interview::class, 'cme_group_id');
    }

    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    

    
    public function candidates()
    {
        return $this->hasManyThrough(
            User::class,
            Interview::class,
            'cme_group_id',
            'id',
            'id',
            'candidate_id'
        );
    }
    
    
    public function getFormattedSessionTimeAttribute()
    {
        return $this->session_time === 'morning' ? 'Matin (9h-12h)' : 'Après-midi (14h-17h)';
    }
    
    
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->session_date)->locale('fr')->isoFormat('dddd D MMMM YYYY');
    }
    
    
    public function getCandidateCountAttribute()
    {
        return $this->interviews->count();
    }
    
    
    public function getIsFullAttribute()
    {
        return $this->interviews->count() >= 4;
    }
    
    
    public function getStatusAttribute()
    {
        $today = Carbon::today();
        
        if ($this->session_date->gt($today)) {
            return 'upcoming';
        } elseif ($this->session_date->eq($today)) {
            return 'in_progress';
        } else {
            return 'completed';
        }
    }
    
    
    public function getStatusBadgeAttribute()
    {
        $status = $this->status;
        
        if ($status === 'upcoming') {
            return '<span class="badge bg-primary">À venir</span>';
        } elseif ($status === 'in_progress') {
            return '<span class="badge bg-warning">En cours</span>';
        } else {
            return '<span class="badge bg-success">Terminé</span>';
        }
    }
}