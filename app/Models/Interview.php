<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'staff_id', 'date', 'start_time', 'end_time',
        'location', 'type', 'status', 'notes'
    ];
    
    protected $casts = [
        'date' => 'date',
    ];
    
    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}