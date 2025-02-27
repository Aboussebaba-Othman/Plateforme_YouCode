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
    
    $quiz = Quiz::first(); // Assuming there's only one quiz for now
    
    // Check if the candidate has already attempted the quiz
    $candidate = Auth::user()->candidate;
    $attempts = $candidate->quizAttempts()
        ->where('quiz_id', $quiz->id)
        ->whereIn('status', ['passed', 'failed'])
        ->first();
        
    if ($attempts) {
        $status = $attempts->status;
        if ($status === 'passed') {
            return redirect()->route('candidate.profile')
                ->with('success', 'You have already passed the quiz!');
        } else {
            return redirect()->route('candidate.profile')
                ->with('error', 'You have failed the quiz and cannot retake it.');
        }
    }
    
    // Check if there's an ongoing attempt
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
    
    // Check if already attempted (passed or failed)
    $attempt = $candidate->quizAttempts()
        ->where('quiz_id', $quiz->id)
        ->whereIn('status', ['passed', 'failed'])
        ->first();
        
    if ($attempt) {
        $status = $attempt->status;
        if ($status === 'passed') {
            return redirect()->route('candidate.profile')
                ->with('success', 'You have already passed the quiz!');
        } else {
            return redirect()->route('candidate.profile')
                ->with('error', 'You have failed the quiz and cannot retake it.');
        }
    }
    
    // Check for existing active attempt
    $attempt = $candidate->quizAttempts()
        ->where('quiz_id', $quiz->id)
        ->where('status', 'started')
        ->first();
        
    // Create new attempt if none exists
    if (!$attempt) {
        $attempt = new QuizAttempt([
            'candidate_id' => $candidate->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'status' => 'started',
            'current_question' => 0, // Start with the first question
        ]);
        $attempt->save();
    }
    
    // Check if time has expired
    if ($attempt->isTimedOut()) {
        $attempt->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
        
        return redirect()->route('candidate.profile')
            ->with('error', 'Your quiz time has expired. You cannot retake the quiz.');
    }
    
    return redirect()->route('candidate.quiz.question', $attempt->id);
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
    public function showQuestion($attemptId)
{
    $attempt = QuizAttempt::findOrFail($attemptId);
    
    // Verify this attempt belongs to the authenticated candidate
    if ($attempt->candidate_id !== Auth::user()->candidate->id) {
        abort(403, 'Unauthorized action.');
    }
    
    // Check if already completed
    if ($attempt->status !== 'started') {
        if ($attempt->status === 'passed') {
            return redirect()->route('candidate.profile')
                ->with('success', 'You have already passed the quiz!');
        } else {
            return redirect()->route('candidate.profile')
                ->with('error', 'You have failed the quiz and cannot retake it.');
        }
    }
    
    // Check if time has expired
    if ($attempt->isTimedOut()) {
        $attempt->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
        
        return redirect()->route('candidate.profile')
            ->with('error', 'Your quiz time has expired. You cannot retake the quiz.');
    }
    
    $quiz = $attempt->quiz;
    $questions = $quiz->questions()->orderBy('id')->get();
    
    // Check if we've reached the end of the questions
    if ($attempt->current_question >= $questions->count()) {
        return $this->evaluateQuiz($attempt);
    }
    
    $question = $questions[$attempt->current_question];
    
    return view('candidate.quiz.question', compact('quiz', 'attempt', 'question'));
}

public function answerQuestion(Request $request, $attemptId)
{
    $attempt = QuizAttempt::findOrFail($attemptId);
    
    // Verify this attempt belongs to the authenticated candidate
    if ($attempt->candidate_id !== Auth::user()->candidate->id) {
        abort(403, 'Unauthorized action.');
    }
    
    // Check if already completed
    if ($attempt->status !== 'started') {
        return redirect()->route('candidate.quiz.results', $attempt->id);
    }
    
    // Check if time has expired
    if ($attempt->isTimedOut()) {
        $attempt->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
        
        return redirect()->route('candidate.profile')
            ->with('error', 'Your quiz time has expired. You cannot retake the quiz.');
    }
    
    $quiz = $attempt->quiz;
    $questions = $quiz->questions()->orderBy('id')->get();
    
    // Check if we've reached the end of the questions
    if ($attempt->current_question >= $questions->count()) {
        return $this->evaluateQuiz($attempt);
    }
    
    $question = $questions[$attempt->current_question];
    $questionId = $question->id;
    
    // Get the answer
    $answerId = $request->input('answer');
    
    if (!$answerId) {
        return redirect()->back()->with('error', 'Please select an answer.');
    }
    
    // Save the answer
    $answers = $attempt->answers ?? [];
    $answers[$questionId] = $answerId;
    $attempt->answers = $answers;
    
    // Move to the next question
    $attempt->current_question = $attempt->current_question + 1;
    $attempt->save();
    
    // Check if that was the last question
    if ($attempt->current_question >= $questions->count()) {
        return $this->evaluateQuiz($attempt);
    }
    
    return redirect()->route('candidate.quiz.question', $attempt->id);
}

private function evaluateQuiz($attempt)
{
    $quiz = $attempt->quiz;
    $questions = $quiz->questions;
    $answers = $attempt->answers ?? [];
    $score = 0;
    
    foreach ($questions as $question) {
        $questionId = $question->id;
        
        if (isset($answers[$questionId])) {
            $answerId = $answers[$questionId];
            
            $answer = $question->answers()->where('id', $answerId)->first();
            if ($answer && $answer->is_correct) {
                $score += $question->points;
            }
        }
    }
    
    $passed = $score >= $quiz->passing_score;
    $status = $passed ? 'passed' : 'failed';
    
    $attempt->update([
        'score' => $score,
        'status' => $status,
        'completed_at' => now(),
    ]);
    
    // Update candidate status if passed
    if ($passed) {
        $candidate = Auth::user()->candidate;
        $candidate->update(['status' => 'quiz_passed']);
    }
    
    return redirect()->route('candidate.quiz.results', $attempt->id);
}
}