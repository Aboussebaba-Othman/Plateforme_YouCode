<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:staff']);
    }
    
    /**
     * Display the staff dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffId = Auth::id();
        
        // Get upcoming interviews
        $upcomingInterviews = Interview::where('staff_id', $staffId)
            ->where('interview_date', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->with('candidate')
            ->orderBy('interview_date')
            ->orderBy('interview_time')
            ->take(5)
            ->get();
            
        // Get past interviews without feedback
        $interviewsWithoutFeedback = Interview::where('staff_id', $staffId)
            ->where(function($query) {
                $query->where('interview_date', '<', Carbon::today())
                      ->orWhere('status', 'completed');
            })
            ->whereDoesntHave('feedbacks', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->with('candidate')
            ->orderBy('interview_date', 'desc')
            ->get();
        
        // Get stats
        $stats = [
            'total_interviews' => Interview::where('staff_id', $staffId)->count(),
            'upcoming_interviews' => Interview::where('staff_id', $staffId)
                ->where('interview_date', '>=', Carbon::today())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'completed_interviews' => Interview::where('staff_id', $staffId)
                ->where('status', 'completed')
                ->count(),
            'pending_feedback' => $interviewsWithoutFeedback->count(),
        ];
        
        return view('staff.dashboard', compact('upcomingInterviews', 'interviewsWithoutFeedback', 'stats'));
    }
}