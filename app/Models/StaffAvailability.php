<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAvailability extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'day_of_week', 'start_time', 'end_time', 'is_available'
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}