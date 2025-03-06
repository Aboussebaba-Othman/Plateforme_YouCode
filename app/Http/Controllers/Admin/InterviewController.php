<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\User;
use App\Models\StaffAvailability;
use App\Mail\InterviewInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    /**
     * Display a listing of the interviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $upcomingInterviews = Interview::upcoming()
            ->with(['candidate', 'staff'])
            ->orderBy('interview_date')
            ->orderBy('interview_time')
            ->get();
            
        $pastInterviews = Interview::past()
            ->with(['candidate', 'staff'])
            ->orderBy('interview_date', 'desc')
            ->orderBy('interview_time', 'desc')
            ->get();
            
        return view('admin.interviews.index', compact('upcomingInterviews', 'pastInterviews'));
    }

    /**
     * Show the form for creating a new interview.
     *
     * @return \Illuminate\Http\Response
     */
   /**
 * Show the form for creating a new interview.
 *
 * @return \Illuminate\Http\Response
 */
public function create(Request $request)
    {
        // Check if candidate_id is provided in the request
        $candidateId = $request->query('candidate_id');
        
        if ($candidateId) {
            // If candidate_id is provided, fetch the candidate
            $candidate = User::withRole('candidate')->findOrFail($candidateId);
        } else {
            // If no candidate_id is provided, show all candidates to choose from
            $candidates = User::withRole('candidate')->get();            
            // Get staff members
            $staffMembers = User::withRole('staff')->get();
            
            return view('admin.interviews.select-candidate', compact('candidates', 'staffMembers'));
        }
        
        // Get staff members for dropdown
        $staffMembers = User::withRole('staff')->get();
        
        return view('admin.interviews.create', compact('candidate', 'staffMembers'));
    }

    /**
     * Store a newly created interview in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
 * Store a newly created interview in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'candidate_id' => 'required|exists:users,id',
        'type' => 'required|in:technical,administrative,CME',
        'location' => 'required|string',
        'notes' => 'nullable|string',
        'auto_schedule' => 'sometimes|boolean',
        'staff_id' => 'nullable|required_without:auto_schedule|exists:users,id',
        'date' => 'nullable|required_without:auto_schedule|date|after_or_equal:today',
        'start_time' => 'nullable|required_without:auto_schedule',
        'end_time' => 'nullable|required_without:auto_schedule|after:start_time',
    ]);

    if ($request->input('auto_schedule')) {
        // Auto-schedule logic
        $slot = $this->findNextAvailableSlot();
        
        if (!$slot) {
            return redirect()->back()->with('error', 'Aucun créneau disponible trouvé. Veuillez planifier manuellement.');
        }
        
        $interview = Interview::create([
            'candidate_id' => $validatedData['candidate_id'],
            'staff_id' => $slot['staff_id'],
            'interview_date' => $slot['date'],
            'interview_time' => $slot['time'],
            'interview_type' => $validatedData['type'],
            'location' => $validatedData['location'],
            'notes' => $validatedData['notes'],
            'status' => 'scheduled'
        ]);
    } else {
        // Manual scheduling
        $interview = Interview::create([
            'candidate_id' => $validatedData['candidate_id'],
            'staff_id' => $validatedData['staff_id'],
            'interview_date' => $validatedData['date'],
            'interview_time' => $validatedData['start_time'],
            'interview_type' => $validatedData['type'],
            'location' => $validatedData['location'],
            'notes' => $validatedData['notes'],
            'status' => 'scheduled'
        ]);
    }

    return redirect()->route('admin.interviews.index')->with('success', 'Entretien planifié avec succès.');
}

    /**
     * Display the specified interview.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    /**
 * Display the specified interview.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function show($id)
{
    // Load interview with related candidate and staff
    $interview = Interview::with(['candidate', 'staff'])->findOrFail($id);
    
    // Check if candidate exists
    if (!$interview->candidate) {
        return redirect()->route('admin.interviews.index')
            ->with('error', 'Le candidat associé à cet entretien n\'existe plus.');
    }
    
    return view('admin.interviews.show', compact('interview'));
}

    /**
     * Show the form for editing the specified interview.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function edit(Interview $interview)
    {
        $candidates = User::withRole('candidate')->orderBy('name')->get();
        
        $staff = User::withRole('staff')
            ->whereHas('availabilities')
            ->orderBy('name')
            ->get();
        
        $interviewTypes = [
            Interview::TYPE_TECHNICAL => 'Entretien Technique',
            Interview::TYPE_ADMINISTRATIVE => 'Entretien Administratif',
            Interview::TYPE_CME => 'Entretien CME',
        ];
        
        $statuses = [
            Interview::STATUS_SCHEDULED => 'Programmé',
            Interview::STATUS_COMPLETED => 'Terminé',
            Interview::STATUS_CANCELLED => 'Annulé',
            Interview::STATUS_NO_SHOW => 'Absence',
        ];
        
        return view('admin.interviews.edit', compact('interview', 'candidates', 'staff', 'interviewTypes', 'statuses'));
    }


    /**
     * Update the specified interview in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interview $interview)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
            'staff_id' => 'required|exists:users,id',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'interview_type' => 'required|in:technical,administrative,cme',
            'location' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
        ]);
        
        $interview->update([
            'candidate_id' => $request->candidate_id,
            'staff_id' => $request->staff_id,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'interview_type' => $request->interview_type,
            'location' => $request->location,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);
        
        // Send invitation email if requested
        if ($request->has('send_invitation')) {
            $this->sendInvitation($interview);
            return redirect()->route('admin.interviews.index')
                ->with('success', 'Entretien mis à jour avec succès et invitation envoyée au candidat.');
        }
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Entretien mis à jour avec succès.');
    }

    /**
     * Remove the specified interview from storage.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interview $interview)
    {
        $interview->delete();
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Entretien supprimé avec succès.');
    }
    
    /**
     * Schedule an interview automatically based on staff availability.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scheduleAutomatically(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
            'interview_type' => 'required|in:technical,administrative,cme',
            'location' => 'required|string',
            'notes' => 'nullable|string',
            'send_invitation' => 'sometimes|boolean',
        ]);

        // Get the next available slot from a staff member
        $availableSlot = $this->findNextAvailableSlot();
        
        if (!$availableSlot) {
            return redirect()->route('admin.interviews.create')
                ->with('error', 'Aucun créneau disponible trouvé. Veuillez réessayer plus tard ou créer un entretien manuellement.');
        }
        
        // Create the interview
        $interview = Interview::create([
            'candidate_id' => $request->candidate_id,
            'staff_id' => $availableSlot['staff_id'],
            'interview_date' => $availableSlot['date'],
            'interview_time' => $availableSlot['time'],
            'interview_type' => $request->interview_type,
            'location' => $request->location,
            'notes' => $request->notes,
            'status' => Interview::STATUS_SCHEDULED,
        ]);
        
        // Send invitation email if requested
        if ($request->has('send_invitation')) {
            $this->sendInvitation($interview);
            return redirect()->route('admin.interviews.index')
                ->with('success', 'Entretien programmé automatiquement et invitation envoyée au candidat.');
        }
        
        return redirect()->route('admin.interviews.index')
            ->with('success', 'Entretien programmé automatiquement.');
    }
    
    /**
     * Send interview invitation email to candidate.
     *
     * @param  \App\Models\Interview  $interview
     * @return void
     */
    public function sendInvitation(Interview $interview)
    {
        $candidate = $interview->candidate;
        
        Mail::to($candidate->email)->send(new InterviewInvitation($interview));
        
        // Update interview to indicate invitation sent
        $interview->update([
            'invitation_sent_at' => now(),
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Invitation envoyée avec succès.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Invitation envoyée avec succès.');
    }
    
    /**
     * Update interview status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Interview $interview)
    {
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
        ]);
        
        $interview->update([
            'status' => $request->status,
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Statut mis à jour avec succès.');
    }
    
    /**
     * Find the next available slot for an interview.
     *
     * @return array|null
     */
    // private function findNextAvailableSlot()
    // {
    //     // Get all staff with availability
    //     $staffWithAvailability = User::whereHas('roles', function($query) {
    //             $query->where('name', 'staff');
    //         })
    //         ->whereHas('availability')
    //         ->with('availability')
    //         ->get();
            
    //     if ($staffWithAvailability->isEmpty()) {
    //         return null;
    //     }
        
    //     // Start looking from tomorrow
    //     $startDate = Carbon::tomorrow();
    //     $endDate = Carbon::today()->addDays(30); // Look up to 30 days ahead
        
    //     // For each day in the range
    //     for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
    //         $dayOfWeek = strtolower($date->englishDayOfWeek);
            
    //         // For each staff member
    //         foreach ($staffWithAvailability as $staff) {
    //             // Find their availability for this day of the week
    //             $availability = $staff->availability->where('day_of_week', $dayOfWeek)
    //                 ->where('is_available', true)
    //                 ->first();
                
    //             if (!$availability) {
    //                 continue; // Staff not available on this day
    //             }
                
    //             $startTime = Carbon::parse($availability->start_time);
    //             $endTime = Carbon::parse($availability->end_time);
                
    //             // Check for existing interviews for this staff on this day
    //             $existingInterviews = Interview::where('staff_id', $staff->id)
    //                 ->whereDate('interview_date', $date->toDateString())
    //                 ->where('status', Interview::STATUS_SCHEDULED)
    //                 ->orderBy('interview_time')
    //                 ->get();
                
    //             // If no existing interviews, use the start time
    //             if ($existingInterviews->isEmpty()) {
    //                 return [
    //                     'staff_id' => $staff->id,
    //                     'date' => $date->toDateString(),
    //                     'time' => $startTime->format('H:i:s'),
    //                 ];
    //             }
                
    //             // Find a gap between existing interviews
    //             $previousEndTime = $startTime;
                
    //             foreach ($existingInterviews as $interview) {
    //                 $interviewTime = Carbon::parse($interview->interview_time);
                    
    //                 // If there's at least 1 hour gap before this interview
    //                 if ($interviewTime->diffInMinutes($previousEndTime) >= 60) {
    //                     return [
    //                         'staff_id' => $staff->id,
    //                         'date' => $date->toDateString(),
    //                         'time' => $previousEndTime->format('H:i:s'),
    //                     ];
    //                 }
                    
    //                 // Set the end time for this interview (assuming 1 hour duration)
    //                 $previousEndTime = $interviewTime->copy()->addHour();
    //             }
                
    //             // Check if there's a slot after the last interview
    //             if ($previousEndTime->addHour()->lte($endTime)) {
    //                 return [
    //                     'staff_id' => $staff->id,
    //                     'date' => $date->toDateString(),
    //                     'time' => $previousEndTime->format('H:i:s'),
    //                 ];
    //             }
    //         }
    //     }
        
    //     return null; // No available slots found
    // }
    // Replace the incomplete findNextAvailableSlot method with this complete version:

/**
 * Find the next available slot for an interview.
 *
 * @return array|null
 */
private function findNextAvailableSlot()
{
    // Get all staff with availability
    $staffWithAvailability = User::role('staff')
        ->whereHas('availabilities')
        ->with('availabilities')
        ->get();
        
    if ($staffWithAvailability->isEmpty()) {
        return null;
    }
    
    // Start looking from tomorrow
    $startDate = Carbon::tomorrow();
    $endDate = Carbon::today()->addDays(30); // Look up to 30 days ahead
    
    // For each day in the range
    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
        $dayOfWeek = strtolower($date->englishDayOfWeek);
        
        // For each staff member
        foreach ($staffWithAvailability as $staff) {
            // Find their availability for this day of the week
            $availability = $staff->availabilities->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->first();
            
            if (!$availability) {
                continue; // No availability on this day
            }
            
            $startTime = Carbon::parse($availability->start_time);
            $endTime = Carbon::parse($availability->end_time);
            
            // Check for existing interviews for this staff on this day
            $existingInterviews = Interview::where('staff_id', $staff->id)
                ->whereDate('interview_date', $date->toDateString())
                ->where('status', '!=', Interview::STATUS_CANCELLED)
                ->orderBy('interview_time')
                ->get();
            
            // If no existing interviews, use the start time
            if ($existingInterviews->isEmpty()) {
                return [
                    'staff_id' => $staff->id,
                    'date' => $date->toDateString(),
                    'time' => $startTime->format('H:i:s'),
                    'end_time' => $startTime->copy()->addHour()->format('H:i:s')
                ];
            }
            
            // Find a gap between existing interviews
            $previousEndTime = Carbon::parse($availability->start_time);
            
            foreach ($existingInterviews as $interview) {
                $interviewStart = Carbon::parse($interview->interview_time);
                
                // If there's at least a 1-hour gap between previous end time and this interview
                if ($interviewStart->diffInMinutes($previousEndTime) >= 60) {
                    return [
                        'staff_id' => $staff->id,
                        'date' => $date->toDateString(),
                        'time' => $previousEndTime->format('H:i:s'),
                        'end_time' => $previousEndTime->copy()->addHour()->format('H:i:s')
                    ];
                }
                
                // Set previous end time to end of this interview (estimate 1 hour duration)
                $previousEndTime = $interviewStart->copy()->addHour();
            }
            
            // Check if there's time after the last interview
            $lastInterviewEnd = $previousEndTime;
            if ($lastInterviewEnd->format('H:i') < $endTime->format('H:i') && 
                $endTime->diffInMinutes($lastInterviewEnd) >= 60) {
                return [
                    'staff_id' => $staff->id,
                    'date' => $date->toDateString(),
                    'time' => $lastInterviewEnd->format('H:i:s'),
                    'end_time' => $lastInterviewEnd->copy()->addHour()->format('H:i:s')
                ];
            }
        }
    }
    
    return null; // No available slots found
}
}