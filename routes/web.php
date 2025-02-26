<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Auth::routes(['verify' => true]);
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