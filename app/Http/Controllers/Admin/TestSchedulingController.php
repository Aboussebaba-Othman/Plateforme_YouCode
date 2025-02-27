<?php

namespace App\Http\Controllers\Admin;

use App\Models\Candidate;
use App\Models\User;
use App\Models\TestSession;
use App\Models\TestAppointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\TestAppointmentConfirmation;

class TestSchedulingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function index()
    {
        $upcomingSessions = TestSession::where('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(10);
            
        $pastSessions = TestSession::where('date', '<', now())
            ->orderBy('date', 'desc')
            ->orderBy('start_time')
            ->paginate(10);
            
        return view('admin.test.index', compact('upcomingSessions', 'pastSessions'));
    }
    
    public function createSession()
    {
        $staff = User::role('staff')->get();
        return view('admin.test.create_session', compact('staff'));
    }
    
    public function storeSession(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'staff_id' => 'required|exists:users,id'
        ]);
        
        $session = new TestSession();
        $session->date = $request->date;
        $session->start_time = $request->start_time;
        $session->end_time = $request->end_time;
        $session->capacity = $request->capacity;
        $session->location = $request->location;
        $session->staff_id = $request->staff_id;
        $session->save();
        
        return redirect()->route('admin.test.sessions')
            ->with('success', 'Test session created successfully');
    }
    
    public function editSession($id)
    {
        $session = TestSession::findOrFail($id);
        $staff = User::role('staff')->get();
        
        // Check if there are any appointments
        $hasAppointments = $session->appointments()->count() > 0;
        
        return view('admin.test.edit_session', compact('session', 'staff', 'hasAppointments'));
    }
    
    public function updateSession(Request $request, $id)
    {
        $session = TestSession::findOrFail($id);
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:' . $session->appointments()->count(),
            'location' => 'required|string',
            'staff_id' => 'required|exists:users,id'
        ]);
        
        $session->date = $request->date;
        $session->start_time = $request->start_time;
        $session->end_time = $request->end_time;
        $session->capacity = $request->capacity;
        $session->location = $request->location;
        $session->staff_id = $request->staff_id;
        $session->save();
        
        return redirect()->route('admin.test.sessions')
            ->with('success', 'Test session updated successfully');
    }
    
    public function deleteSession($id)
    {
        $session = TestSession::findOrFail($id);
        
        // Check if there are any appointments
        if ($session->appointments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete session with existing appointments');
        }
        
        $session->delete();
        
        return redirect()->route('admin.test.sessions')
            ->with('success', 'Test session deleted successfully');
    }
    
    // Schedule candidates for tests
    public function scheduleCandidates()
    {
        // Get candidates who passed the quiz but don't have a test scheduled
        $eligibleCandidates = Candidate::with('user')
            ->where('status', 'quiz_passed')
            ->whereDoesntHave('testAppointment')
            ->latest()
            ->paginate(10);
            
        // Get upcoming test sessions with available capacity
        $availableSessions = TestSession::where('date', '>=', now())
            ->whereRaw('capacity > (SELECT COUNT(*) FROM test_appointments WHERE test_session_id = test_sessions.id)')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
            
        return view('admin.test.schedule', compact('eligibleCandidates', 'availableSessions'));
    }
    
    public function assignTest(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'session_id' => 'required|exists:test_sessions,id'
        ]);
        
        $candidate = Candidate::findOrFail($request->candidate_id);
        $session = TestSession::findOrFail($request->session_id);
        
        // Check if candidate already has an appointment
        if ($candidate->testAppointment) {
            return redirect()->back()
                ->with('error', 'Candidate already has a test appointment');
        }
        
        // Check if session has available capacity
        $currentAppointments = $session->appointments()->count();
        if ($currentAppointments >= $session->capacity) {
            return redirect()->back()
                ->with('error', 'Selected session is full');
        }
        
        // Create the appointment
        $appointment = new TestAppointment();
        $appointment->candidate_id = $candidate->id;
        $appointment->test_session_id = $session->id;
        $appointment->status = 'scheduled';
        $appointment->save();
        
        // Update candidate status
        $candidate->status = 'test_scheduled';
        $candidate->save();
        
        // Send confirmation email
        Mail::to($candidate->user->email)
            ->send(new TestAppointmentConfirmation($appointment));
        
        return redirect()->route('admin.test.schedule')
            ->with('success', 'Test scheduled successfully and confirmation email sent');
    }
    
    // View session details with all appointments
    public function viewSession($id)
    {
        $session = TestSession::with(['appointments.candidate.user', 'staff'])
            ->findOrFail($id);
            
        return view('admin.test.view_session', compact('session'));
    }
    
    // Record test results
    public function recordResults($appointmentId)
    {
        $appointment = TestAppointment::with(['candidate.user', 'session'])
            ->findOrFail($appointmentId);
            
        return view('admin.test.record_results', compact('appointment'));
    }
    
    public function saveResults(Request $request, $appointmentId)
    {
        $request->validate([
            'technical_score' => 'required|integer|min:0|max:100',
            'administrative_score' => 'required|integer|min:0|max:100',
            'cme_score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:passed,failed,no_show'
        ]);
        
        $appointment = TestAppointment::findOrFail($appointmentId);
        $appointment->technical_score = $request->technical_score;
        $appointment->administrative_score = $request->administrative_score;
        $appointment->cme_score = $request->cme_score;
        $appointment->notes = $request->notes;
        $appointment->status = $request->status;
        $appointment->completed_at = now();
        $appointment->save();
        
        // Update candidate status if needed
        if ($request->status === 'passed') {
            $appointment->candidate->status = 'test_passed';
            $appointment->candidate->save();
        } elseif ($request->status === 'failed') {
            $appointment->candidate->status = 'test_failed';
            $appointment->candidate->save();
        }
        
        return redirect()->route('admin.test.view', $appointment->test_session_id)
            ->with('success', 'Test results recorded successfully');
    }
}