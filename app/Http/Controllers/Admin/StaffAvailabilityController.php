<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffAvailability;
use Illuminate\Http\Request;

class StaffAvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function edit($staffId)
    {
        $staff = User::findOrFail($staffId);
                $availabilities = $staff->availabilities;
        
        if ($availabilities->isEmpty()) {            
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            foreach ($days as $day) {
                StaffAvailability::create([
                    'user_id' => $staff->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'is_available' => true
                ]);
            }
                        StaffAvailability::create([
                'user_id' => $staff->id,
                'day_of_week' => 'saturday',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_available' => false
            ]);
            
            StaffAvailability::create([
                'user_id' => $staff->id,
                'day_of_week' => 'sunday',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_available' => false
            ]);
            
            $availabilities = $staff->availabilities()->get();
        }
        
        return view('admin.staff.availability', compact('staff', 'availabilities'));
    }
    
    public function update(Request $request, $staffId)
    {
        $staff = User::findOrFail($staffId);
        
        foreach ($request->availabilities as $day => $data) {
            $availability = $staff->availabilities()->where('day_of_week', $day)->first();
            
            if ($availability) {
                $availability->update([
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'is_available' => isset($data['is_available']) ? true : false
                ]);
            }
        }
        
        return redirect()->route('admin.staff.availability.edit', $staffId)
            ->with('success', 'Staff availability updated successfully');
    }
}