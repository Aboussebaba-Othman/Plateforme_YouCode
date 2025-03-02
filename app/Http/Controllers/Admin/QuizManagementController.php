<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class QuizManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->latest()->paginate(10);
        return view('admin.quiz.index', compact('quizzes'));
    }
    
    public function create()
    {
        return view('admin.quiz.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $quiz = new Quiz();
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->time_limit = $request->time_limit;
        $quiz->passing_score = $request->passing_score;
        $quiz->is_active = $request->has('is_active');
        $quiz->save();
        
        return redirect()->route('admin.quiz.index')->with('success', 'Quiz created successfully');
    }
    
    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        return view('admin.quiz.edit', compact('quiz'));
    }
    
    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->time_limit = $request->time_limit;
        $quiz->passing_score = $request->passing_score;
        $quiz->is_active = $request->has('is_active');
        $quiz->save();
        
        return redirect()->back()->with('success', 'Quiz updated successfully');
    }
    
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        
        if ($quiz->attempts()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete quiz with attempts');
        }
        
        DB::transaction(function() use ($quiz) {
            foreach ($quiz->questions as $question) {
                $question->answers()->delete();
            }
            $quiz->questions()->delete();
            $quiz->delete();
        });
        
        return redirect()->route('admin.quiz.index')->with('success', 'Quiz deleted successfully');
    }
    
 
    public function createQuestion($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        return view('admin.quiz.questions.create', compact('quiz'));
    }

    public function storeQuestion(Request $request, $quizId)
    {
        $request->validate([
            'content' => 'required|string',
            'points' => 'required|integer|min:1',
            'type' => 'required|in:single,multiple',
            'answers.*.content' => 'required|string',
            'correct_answer' => 'required|integer|min:0'
        ]);
        
        $quiz = Quiz::findOrFail($quizId);
        
        DB::transaction(function() use ($request, $quiz) {
            $question = new Question();
            $question->quiz_id = $quiz->id;
            $question->content = $request->content;
            $question->points = $request->points;
            $question->type = $request->type;
            $question->save();

            foreach ($request->answers as $index => $answerData) {
                $answer = new Answer();
                $answer->question_id = $question->id;
                $answer->content = $answerData['content'];
                $answer->is_correct = $index == $request->correct_answer;
                $answer->save();
            }
        });
        
        return redirect()->back()->with('success', 'Question added successfully');
    }

    public function editQuestion($quizId, $questionId)
{
    $quiz = Quiz::findOrFail($quizId);
    $question = Question::findOrFail($questionId);
    return view('admin.quiz.questions.edit', compact('quiz', 'question', 'quizId'));
}
    
    
    public function updateQuestion(Request $request, $quizId, $questionId)
    {
        $request->validate([
            'content' => 'required|string',
            'points' => 'required|integer|min:1',
            'type' => 'required|in:single,multiple',
            'answers.*.content' => 'required|string',
            'correct_answer' => 'required|integer|min:0'
        ]);
        
        $quiz = Quiz::findOrFail($quizId);
        $question = Question::findOrFail($questionId);
        
        DB::transaction(function() use ($request, $question) {
            $question->content = $request->content;
            $question->points = $request->points;
            $question->type = $request->type;
            $question->save();
            
            $question->answers()->delete();
            
            foreach ($request->answers as $index => $answerData) {
                $answer = new Answer();
                $answer->question_id = $question->id;
                $answer->content = $answerData['content'];
                $answer->is_correct = $index == $request->correct_answer;
                $answer->save();
            }
        });
        
        return redirect()->back()->with('success', 'Question updated successfully');
    }
    
    public function destroyQuestion($quizId, $questionId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = Question::findOrFail($questionId);
        
        $question->answers()->delete();
        $question->delete();
        
        return redirect()->back()->with('success', 'Question deleted successfully');
    }
    
    public function statistics($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $totalAttempts = $quiz->attempts()->count();
        $passedAttempts = $quiz->attempts()->where('status', 'passed')->count();
        $failedAttempts = $quiz->attempts()->where('status', 'failed')->count();
        $averageScore = $quiz->attempts()->avg('score') ?? 0;
        
        $questionStats = $quiz->questions()
            ->map(function ($question) use ($quiz) {
                $total = $quiz->attempts()->count();
                if ($total > 0) {
                    $correctAnswers = DB::table('quiz_attempts')
                        ->where('quiz_id', $quiz->id)
                        ->whereJsonContains('answers->' . $question->id, function ($query) use ($question) {
                            $query->select('id')
                                ->from('answers')
                                ->where('question_id', $question->id)
                                ->where('is_correct', true)
                                ->first();
                        })
                        ->count();
                    
                    $question->success_rate = round(($correctAnswers / $total) * 100, 2);
                }
                
                return $question;
            });
        
        return view('admin.quiz.statistics', compact('quiz', 'totalAttempts', 'passedAttempts', 'failedAttempts', 'averageScore', 'questionStats'));
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);
        return view('admin.quiz.show', compact('quiz'));
    }
}