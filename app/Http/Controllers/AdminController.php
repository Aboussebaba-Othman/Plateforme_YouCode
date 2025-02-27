<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function dashboard()
    {
        // Get statistics for dashboard
        $stats = [
            'total_candidates' => Candidate::count(),
            'pending_documents' => Candidate::where('status', 'documents_submitted')->count(),
            'approved_documents' => Candidate::where('status', 'documents_approved')->count(),
            'quiz_passed' => Candidate::where('status', 'quiz_passed')->count(),
            'test_scheduled' => Candidate::where('status', 'test_scheduled')->count(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    public function candidatesList()
    {
        $candidates = Candidate::with('user')->latest()->paginate(10);
        return view('admin.candidates', compact('candidates'));
    }

    public function viewCandidate($id)
    {
        $candidate = Candidate::with('user')->findOrFail($id);
        return view('admin.candidate_details', compact('candidate'));
    }

    public function updateCandidateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,documents_submitted,documents_approved,quiz_passed,test_scheduled'
        ]);

        $candidate = Candidate::findOrFail($id);
        $candidate->status = $request->status;
        $candidate->save();

        return redirect()->back()->with('success', 'Candidate status updated successfully');
    }
}