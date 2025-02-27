@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::user()->isCandidate())
                        <h2>Welcome, Candidate!</h2>
                        <p>You can manage your application process using the links below.</p>
                        
                        <div class="list-group mt-4">
                            <a href="{{ route('candidate.profile') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Profile and Documents</h5>
                                </div>
                                <p class="mb-1">View and update your personal information and documents.</p>
                            </a>
                            
                            @if(Auth::user()->candidate && Auth::user()->candidate->status == 'documents_approved')
                                <a href="{{ route('candidate.quiz.index') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Assessment Quiz</h5>
                                    </div>
                                    <p class="mb-1">Take the assessment quiz to proceed to the next stage.</p>
                                </a>
                            @endif
                        </div>
                    @else
                        <h2>Welcome!</h2>
                        <p>You are logged in to the application system.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection