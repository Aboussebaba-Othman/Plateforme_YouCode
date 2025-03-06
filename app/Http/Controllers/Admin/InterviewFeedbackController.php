<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\InterviewFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewFeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin,staff']);
    }

    /**
     * Show form to add feedback for an interview.
     *
     * @param  int  $interviewId
     * @return \Illuminate\Http\Response
     */
    public function create($interviewId)
    {
        $interview = Interview::with(['candidate', 'staff'])->findOrFail($interviewId);
        
        // Check if user is the assigned staff or an admin
        if (!Auth::user()->hasRole('admin') && Auth::id() != $interview->staff_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à ajouter un feedback pour cet entretien.');
        }
        
        // Check if feedback already exists
        $existingFeedback = InterviewFeedback::where('interview_id', $interviewId)
            ->where('staff_id', Auth::id())
            ->first();
            
        if ($existingFeedback) {
            return redirect()->route('admin.feedback.edit', $existingFeedback->id);
        }
        
        return view('admin.feedback.create', compact('interview'));
    }

    /**
     * Store a newly created feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $interviewId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        
        // Check if user is the assigned staff or an admin
        if (!Auth::user()->hasRole('admin') && Auth::id() != $interview->staff_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à ajouter un feedback pour cet entretien.');
        }
        
        $validatedData = $request->validate([
            'comments' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|in:hire,consider,reject',
        ]);
        
        InterviewFeedback::create([
            'interview_id' => $interviewId,
            'staff_id' => Auth::id(),
            'comments' => $validatedData['comments'],
            'rating' => $validatedData['rating'],
            'recommendation' => $validatedData['recommendation'],
        ]);
        
        // Update interview status if needed
        if ($interview->status === 'scheduled') {
            $interview->update(['status' => 'completed']);
        }
        
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.interviews.show', $interviewId)
                ->with('success', 'Feedback ajouté avec succès.');
        } else {
            return redirect()->route('staff.interviews.index')
                ->with('success', 'Feedback ajouté avec succès.');
        }
    }

    /**
     * Show the form for editing the specified feedback.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feedback = InterviewFeedback::with(['interview.candidate', 'interview.staff'])
            ->findOrFail($id);
            
        // Check if user is the feedback author or an admin
        if (!Auth::user()->hasRole('admin') && Auth::id() != $feedback->staff_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier ce feedback.');
        }
        
        return view('admin.feedback.edit', compact('feedback'));
    }

    /**
     * Update the specified feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $feedback = InterviewFeedback::findOrFail($id);
        
        // Check if user is the feedback author or an admin
        if (!Auth::user()->hasRole('admin') && Auth::id() != $feedback->staff_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier ce feedback.');
        }
        
        $validatedData = $request->validate([
            'comments' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|in:hire,consider,reject',
        ]);
        
        $feedback->update([
            'comments' => $validatedData['comments'],
            'rating' => $validatedData['rating'],
            'recommendation' => $validatedData['recommendation'],
        ]);
        
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.interviews.show', $feedback->interview_id)
                ->with('success', 'Feedback mis à jour avec succès.');
        } else {
            return redirect()->route('staff.interviews.index')
                ->with('success', 'Feedback mis à jour avec succès.');
        }
    }

    /**
     * Remove the specified feedback from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feedback = InterviewFeedback::findOrFail($id);
        
        // Only admins can delete feedback
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce feedback.');
        }
        
        $interviewId = $feedback->interview_id;
        $feedback->delete();
        
        return redirect()->route('admin.interviews.show', $interviewId)
            ->with('success', 'Feedback supprimé avec succès.');
    }
}