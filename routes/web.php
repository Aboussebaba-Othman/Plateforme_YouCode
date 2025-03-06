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
use App\Http\Controllers\Admin\InterviewController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Admin\CmeGroupController;
use App\Http\Controllers\Admin\InterviewFeedbackController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\InterviewController as StaffInterviewController;

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

// Routes administrateur
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/candidates', [AdminController::class, 'candidatesList'])->name('candidates');
    Route::get('/candidates/{id}', [AdminController::class, 'viewCandidate'])->name('candidate.view');
    Route::post('/candidates/{id}/status', [AdminController::class, 'updateCandidateStatus'])->name('candidate.update.status');

    // Gestion des quiz
    Route::prefix('quiz')->name('quiz.')->group(function () {
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
    
    // Gestion des entretiens
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('index');
        Route::get('/create', [InterviewController::class, 'create'])->name('create');
        Route::post('/', [InterviewController::class, 'store'])->name('store');
        Route::get('/{interview}', [InterviewController::class, 'show'])->name('show');
        Route::get('/{interview}/edit', [InterviewController::class, 'edit'])->name('edit');
        Route::put('/{interview}', [InterviewController::class, 'update'])->name('update');
        Route::delete('/{interview}', [InterviewController::class, 'destroy'])->name('destroy');
        Route::post('/{interview}/send-invitation', [InterviewController::class, 'sendInvitation'])->name('send-invitation');
        Route::post('/auto-schedule', [InterviewController::class, 'scheduleAutomatically'])->name('auto-schedule');
        Route::post('/schedule-automatically', [InterviewController::class, 'scheduleAutomatically'])->name('schedule.auto');
        Route::post('/{interview}/update-status', [InterviewController::class, 'updateStatus'])->name('update.status');
    });
    
    // Gestion des disponibilités du staff
    Route::get('/staff/{id}/availability', [StaffAvailabilityController::class, 'edit'])->name('staff.availability');
    Route::put('/staff/{id}/availability', [StaffAvailabilityController::class, 'update'])->name('staff.availability.update');
    
    // Gestion des groupes CME
    Route::prefix('cme')->name('cme.')->group(function () {
        Route::get('/', [CmeGroupController::class, 'index'])->name('index');
        Route::get('/create', [CmeGroupController::class, 'create'])->name('create');
        Route::post('/', [CmeGroupController::class, 'store'])->name('store');
        Route::get('/{id}', [CmeGroupController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CmeGroupController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CmeGroupController::class, 'update'])->name('update');
        Route::delete('/{id}', [CmeGroupController::class, 'destroy'])->name('destroy');
    });
    
    // Gestion des feedbacks d'entretien
    Route::prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/interview/{interviewId}', [InterviewFeedbackController::class, 'create'])->name('create');
        Route::post('/interview/{interviewId}', [InterviewFeedbackController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InterviewFeedbackController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InterviewFeedbackController::class, 'update'])->name('update');
        Route::delete('/{id}', [InterviewFeedbackController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Routes existantes
    Route::get('/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/interviews', [App\Http\Controllers\Staff\InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/{id}', [App\Http\Controllers\Staff\InterviewController::class, 'show'])->name('interviews.show');
    
    // Nouvelles routes pour les actions
    Route::post('/interviews/{id}/mark-completed', [App\Http\Controllers\Staff\InterviewController::class, 'markCompleted'])->name('interviews.mark-completed');
    Route::post('/interviews/{id}/mark-no-show', [App\Http\Controllers\Staff\InterviewController::class, 'markNoShow'])->name('interviews.mark-no-show');
});