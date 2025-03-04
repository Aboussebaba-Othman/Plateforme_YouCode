<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Interview;
use App\Models\StaffAvailability;
use App\Mail\InterviewInvitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function index()
    {
        $interviews = Interview::with(['candidate', 'staff'])->latest()->paginate(10);
        return view('admin.interviews.index', compact('interviews'));
    }
    
    public function create($candidateId)
    {
        $candidate = User::findOrFail($candidateId);
        $staffMembers = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->get();
        
        return view('admin.interviews.create', compact('candidate', 'staffMembers'));
    }
    
    public function store(Request $request, $candidateId)
    {
        $request->validate([
            'type' => 'required|in:technical,administrative,CME',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'auto_schedule' => 'boolean'
        ]);
        
        $candidate = User::findOrFail($candidateId);
        
        if ($request->has('auto_schedule') && $request->auto_schedule) {
            $interview = $this->autoScheduleInterview($candidate, $request->type, $request->location, $request->notes);
            
            if (!$interview) {
                return redirect()->back()->with('error', 'No available staff or time slots found. Please try again later or schedule manually.');
            }
        } else {
            $request->validate([
                'staff_id' => 'required|exists:users,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ]);
            
            $interview = Interview::create([
                'user_id' => $candidate->id,
                'staff_id' => $request->staff_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'type' => $request->type,
                'notes' => $request->notes
            ]);
        }
        
        // Send email invitation
        Mail::to($candidate->email)->send(new InterviewInvitation($interview));
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Interview scheduled successfully and invitation sent.');
    }
    
    protected function autoScheduleInterview($candidate, $type, $location, $notes = null)
    {
        // Get all staff with their availabilities
        $staffWithAvailability = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->with('availabilities')->get();
        
        if ($staffWithAvailability->isEmpty()) {
            return null;
        }
        
        // Check existing interviews to avoid conflicts
        $existingInterviews = Interview::where('status', 'scheduled')
            ->get()
            ->groupBy('staff_id')
            ->map(function ($interviews) {
                return $interviews->map(function ($interview) {
                    return [
                        'date' => Carbon::parse($interview->date),
                        'start_time' => Carbon::parse($interview->start_time)->format('H:i'),
                        'end_time' => Carbon::parse($interview->end_time)->format('H:i')
                    ];
                });
            });
        
        // Find the next available slot
        $interviewDate = null;
        $interviewStartTime = null;
        $interviewEndTime = null;
        $selectedStaff = null;
        
        // Look for slots in the next 14 days
        for ($dayOffset = 1; $dayOffset <= 14; $dayOffset++) {
            $checkDate = Carbon::today()->addDays($dayOffset);
            $dayOfWeek = strtolower($checkDate->format('l')); // monday, tuesday, etc.
            
            foreach ($staffWithAvailability as $staff) {
                // Skip if staff has no availabilities
                if ($staff->availabilities->isEmpty()) {
                    continue;
                }
                
                // Check if staff is available on this day
                $availabilityForDay = $staff->availabilities
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true)
                    ->first();
                
                if (!$availabilityForDay) {
                    continue;
                }
                
                $startTime = Carbon::parse($availabilityForDay->start_time);
                $endTime = Carbon::parse($availabilityForDay->end_time);
                
                // Default interview duration: 1 hour
                $interviewDuration = 60; // minutes
                
                // Check if there are any existing interviews for this staff on this date
                $staffInterviews = $existingInterviews[$staff->id] ?? collect();
                $conflictFound = false;
                
                // Check each half hour slot
                for ($timeSlot = clone $startTime; $timeSlot->addMinutes($interviewDuration) <= $endTime; $timeSlot->addMinutes(30)) {
                    $slotStart = (clone $timeSlot)->format('H:i');
                    $slotEnd = (clone $timeSlot)->addMinutes($interviewDuration)->format('H:i');
                    
                    // Check for conflicts with existing interviews
                    foreach ($staffInterviews as $existingInterview) {
                        if ($existingInterview['date']->isSameDay($checkDate)) {
                            $existingStart = $existingInterview['start_time'];
                            $existingEnd = $existingInterview['end_time'];
                            
                            // Check if there's an overlap
                            if (
                                ($slotStart < $existingEnd && $slotEnd > $existingStart) ||
                                ($slotStart === $existingStart) ||
                                ($slotEnd === $existingEnd)
                            ) {
                                $conflictFound = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$conflictFound) {
                        // Found an available slot
                        $interviewDate = $checkDate;
                        $interviewStartTime = $slotStart;
                        $interviewEndTime = $slotEnd;
                        $selectedStaff = $staff;
                        break 3; // Break out of all loops
                    }
                }
            }
        }
        
        // If no available slot found
        if (!$selectedStaff || !$interviewDate) {
            return null;
        }
        
        // Create the interview
        return Interview::create([
            'user_id' => $candidate->id,
            'staff_id' => $selectedStaff->id,
            'date' => $interviewDate->format('Y-m-d'),
            'start_time' => $interviewStartTime,
            'end_time' => $interviewEndTime,
            'location' => $location,
            'type' => $type,
            'notes' => $notes
        ]);
    }
    
    public function show($id)
    {
        $interview = Interview::with(['candidate', 'staff'])->findOrFail($id);
        return view('admin.interviews.show', compact('interview'));
    }
    
    public function edit($id)
    {
        $interview = Interview::findOrFail($id);
        $staffMembers = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->get();
        
        return view('admin.interviews.edit', compact('interview', 'staffMembers'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'required|string|max:255',
            'type' => 'required|in:technical,administrative,CME',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        $interview = Interview::findOrFail($id);
        $interview->update($request->all());
        
        // Notify candidate if significant details changed
        if ($interview->wasChanged(['date', 'start_time', 'end_time', 'location'])) {
            Mail::to($interview->candidate->email)
                ->send(new InterviewInvitation($interview, true));
        }
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Interview updated successfully.');
    }
    
    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);
        $interview->delete();
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Interview deleted successfully.');
    }
    
    public function viewSchedule($staffId = null)
    {
        $staffMembers = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->get();
        
        $interviews = [];
        if ($staffId) {
            $interviews = Interview::where('staff_id', $staffId)
                ->where('status', 'scheduled')
                ->where('date', '>=', Carbon::today())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        }
        
        return view('admin.interviews.schedule', compact('staffMembers', 'interviews', 'staffId'));
    }
}