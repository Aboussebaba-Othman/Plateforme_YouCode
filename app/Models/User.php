<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the roles for the user (compatibility method).
     */
    public function roles()
    {
        // This method creates a collection with the single role
        // to maintain compatibility with code that expects a roles() method
        return collect([$this->role]);
    }

    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    /**
     * Check if user has a specific role
     * 
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }
    
    /**
     * Check if user is a candidate
     * 
     * @return bool
     */
    public function isCandidate()
    {
        return $this->hasRole('candidate');
    }

    /**
     * Get the staff availabilities for this user.
     */
    public function availabilities()
    {
        return $this->hasMany(StaffAvailability::class);
    }

    /**
     * Get the interviews where this user is the candidate.
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class, 'candidate_id');
    }

    /**
     * Get the interviews where this user is the staff/examiner.
     */
    public function interviewsAsStaff()
    {
        return $this->hasMany(Interview::class, 'staff_id');
    }
    /**
 * Scope a query to only include users with a specific role.
 */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}