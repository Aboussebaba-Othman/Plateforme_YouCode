<?php

namespace App\Listeners;

use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignCandidateRole
{
    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $candidateRole = Role::where('name', 'candidate')->first();
        
        if ($candidateRole) {
            $event->user->role_id = $candidateRole->id;
            $event->user->save();
        }
    }
}