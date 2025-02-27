<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define gates for authorization
        Gate::define('view-quiz', function ($user) {
            return $user->isCandidate() && 
                   $user->candidate && 
                   $user->candidate->status === 'documents_approved';
        });

        Gate::define('schedule-appointment', function ($user) {
            return $user->isCandidate() && 
                   $user->candidate && 
                   $user->candidate->status === 'quiz_passed';
        });
    }
}