<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified', 'role:candidate'])->group(function () {
    Route::get('/candidate/profile', [CandidateController::class, 'showProfile'])->name('candidate.profile');
    Route::get('/candidate/documents', [CandidateController::class, 'showDocumentForm'])->name('candidate.documents');
    Route::post('/candidate/documents', [CandidateController::class, 'submitDocuments'])->name('candidate.submit.documents');
});
Route::get('/dev/verify-email', function () {
    if (app()->environment('local')) {
        auth()->user()->markEmailAsVerified();
        return redirect()->route('home')->with('success', 'Email verified!');
    }
    return redirect()->route('home');
})->middleware('auth')->name('dev.verify-email');

Route::middleware(['auth', 'verified', 'role:candidate'])->group(function () {
    Route::get('/candidate/quiz', [QuizController::class, 'index'])->name('candidate.quiz.index');
    Route::get('/candidate/quiz/{quiz}/start', [QuizController::class, 'startQuiz'])->name('candidate.quiz.start');
    Route::get('/candidate/quiz/attempt/{attempt}/question', [QuizController::class, 'showQuestion'])->name('candidate.quiz.question');
    Route::post('/candidate/quiz/attempt/{attempt}/answer', [QuizController::class, 'answerQuestion'])->name('candidate.quiz.answer');
    Route::get('/candidate/quiz/results/{attempt}', [QuizController::class, 'showResults'])->name('candidate.quiz.results');
});
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/candidates', [AdminController::class, 'candidatesList'])->name('admin.candidates');
    Route::get('/candidates/{id}', [AdminController::class, 'viewCandidate'])->name('admin.candidate.view');
    Route::post('/candidates/{id}/status', [AdminController::class, 'updateCandidateStatus'])->name('admin.candidate.update.status');
});