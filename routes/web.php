<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\QuizManagementController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::get('/test-email', function () {
    Mail::raw('Test email from Laravel app', function ($message) {
        $message->to(auth()->user()->email)
            ->subject('Test Email');
    });
    
    return 'Test email sent to ' . auth()->user()->email;
})->middleware('auth')->name('test.email');

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

    Route::prefix('quiz')->name('admin.quiz.')->group(function () {
        Route::get('/', [QuizManagementController::class, 'index'])->name('index');
        Route::get('/create', [QuizManagementController::class, 'create'])->name('create');
        Route::post('/', [QuizManagementController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [QuizManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuizManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuizManagementController::class, 'destroy'])->name('destroy');
        
        Route::get('/{quizId}/questions/create', [QuizManagementController::class, 'createQuestion'])->name('question.create');
        Route::post('/{quizId}/questions', [QuizManagementController::class, 'storeQuestion'])->name('question.store');
        Route::get('/{quizId}/questions/{questionId}/edit', [QuizManagementController::class, 'editQuestion'])->name('question.edit');
        Route::put('/{quizId}/questions/{questionId}', [QuizManagementController::class, 'updateQuestion'])->name('question.update');
        Route::delete('/{quizId}/questions/{questionId}', [QuizManagementController::class, 'destroyQuestion'])->name('question.destroy');
        Route::get('/{id}', [QuizManagementController::class, 'show'])->name('show');
    });
});