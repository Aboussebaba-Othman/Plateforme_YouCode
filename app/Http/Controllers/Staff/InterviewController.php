<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interview;
use App\Models\InterviewFeedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:staff']);
    }
    
    /**
     * Display a listing of the interviews for this staff member.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffId = Auth::id();
        
        $upcomingInterviews = Interview::where('staff_id', $staffId)
            ->where('interview_date', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->with('candidate')
            ->orderBy('interview_date')
            ->orderBy('interview_time')
            ->get();
            
        $pastInterviews = Interview::where('staff_id', $staffId)
            ->where(function($query) {
                $query->where('interview_date', '<', Carbon::today())
                      ->orWhere('status', 'completed')
                      ->orWhere('status', 'cancelled')
                      ->orWhere('status', 'no_show');
            })
            ->with(['candidate', 'feedbacks' => function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            }])
            ->orderBy('interview_date', 'desc')
            ->orderBy('interview_time', 'desc')
            ->get();
            
        return view('staff.interviews.index', compact('upcomingInterviews', 'pastInterviews'));
    }
    
    /**
     * Display the specified interview.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staffId = Auth::id();
        
        $interview = Interview::where('id', $id)
            ->where('staff_id', $staffId)
            ->with(['candidate', 'feedbacks' => function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            }])
            ->firstOrFail();
            
        // Check if feedback exists
        $feedback = $interview->feedbacks->first();
        
        return view('staff.interviews.show', compact('interview', 'feedback'));
    }
    
    /**
     * Mark an interview as completed.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markCompleted($id)
    {
        $staffId = Auth::id();
        
        $interview = Interview::where('id', $id)
            ->where('staff_id', $staffId)
            ->firstOrFail();
            
        $interview->update([
            'status' => 'completed'
        ]);
        
        return redirect()->route('staff.interviews.show', $id)
            ->with('success', 'Entretien marqué comme terminé avec succès.');
    }
    
    /**
     * Mark an interview as no-show.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markNoShow($id)
    {
        $staffId = Auth::id();
        
        $interview = Interview::where('id', $id)
            ->where('staff_id', $staffId)
            ->firstOrFail();
            
        $interview->update([
            'status' => 'no_show'
        ]);
        
        return redirect()->route('staff.interviews.show', $id)
            ->with('success', 'Entretien marqué comme absence avec succès.');
    }
}