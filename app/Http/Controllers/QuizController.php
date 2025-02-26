<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:candidate']);
    }
    
    public function index()
    {
        if (!Gate::allows('view-quiz')) {
            return redirect()->route('candidate.profile')
                ->with('error', 'Your documents must be approved before taking the quiz.');
        }
        
        $quiz = Quiz::first(); 
        
        $candidate = Auth::user()->candidate;
        $passedAttempt = $candidate->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'passed')
            ->first();
            
        if ($passedAttempt) {
            return redirect()->route('candidate.profile')
                ->with('success', 'You have already passed the quiz!');
        }
        
        $activeAttempt = $candidate->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'started')
            ->first();
            
        return view('candidate.quiz.index', compact('quiz', 'activeAttempt'));
    }
    
    public function startQuiz($quizId)
    {
        if (!Gate::allows('view-quiz')) {
            return redirect()->route('candidate.profile')
                ->with('error', 'Your documents must be approved before taking the quiz.');
        }
        
        $quiz = Quiz::findOrFail($quizId);
        $candidate = Auth::user()->candidate;
        
        $passedAttempt = $candidate->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'passed')
            ->first();
            
        if ($passedAttempt) {
            return redirect()->route('candidate.profile')
                ->with('success', 'You have already passed the quiz!');
        }
        
        $attempt = $candidate->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'started')
            ->first();
            
        if (!$attempt) {
            $attempt = new QuizAttempt([
                'candidate_id' => $candidate->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'status' => 'started',
            ]);
            $attempt->save();
        }
        
        if ($attempt->isTimedOut()) {
            $attempt->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
            
            return redirect()->route('candidate.quiz.index')
                ->with('error', 'Your quiz time has expired. Please start a new attempt.');
        }
        
        return view('candidate.quiz.take', compact('quiz', 'attempt'));
    }
    
    public function submitQuiz(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        
        if ($attempt->candidate_id !== Auth::user()->candidate->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($attempt->status !== 'started') {
            return redirect()->route('candidate.quiz.index')
                ->with('error', 'This quiz attempt has already been submitted.');
        }
        
        if ($attempt->isTimedOut()) {
            $attempt->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
            
            return redirect()->route('candidate.quiz.index')
                ->with('error', 'Your quiz time has expired.');
        }
        
        $quiz = $attempt->quiz;
        $questions = $quiz->questions;
        
        $answers = [];
        $score = 0;
        
        foreach ($questions as $question) {
            $answerId = $request->input('question_' . $question->id);
            
            if ($answerId) {
                $answers[$question->id] = $answerId;
                
                $answer = $question->answers()->where('id', $answerId)->first();
                if ($answer && $answer->is_correct) {
                    $score += $question->points;
                }
            }
        }
        
        $passed = $score >= $quiz->passing_score;
        $status = $passed ? 'passed' : 'failed';
        
        $attempt->update([
            'answers' => $answers,
            'score' => $score,
            'status' => $status,
            'completed_at' => now(),
        ]);
        
        if ($passed) {
            $candidate = Auth::user()->candidate;
            $candidate->update(['status' => 'quiz_passed']);
        }
        
        return redirect()->route('candidate.quiz.results', $attempt->id);
    }
    
    public function showResults($attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        
        if ($attempt->candidate_id !== Auth::user()->candidate->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $quiz = $attempt->quiz;
        
        return view('candidate.quiz.results', compact('attempt', 'quiz'));
    }
}