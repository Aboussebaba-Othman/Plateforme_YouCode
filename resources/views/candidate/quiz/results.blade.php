@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Quiz Results</div>

                <div class="card-body">
                    <h2>{{ $quiz->title }} - Results</h2>
                    
                    <div class="alert {{ $attempt->status === 'passed' ? 'alert-success' : 'alert-danger' }} mb-4">
                        <h4 class="mb-0">
                            @if ($attempt->status === 'passed')
                                Congratulations! You passed the quiz.
                            @else
                                Sorry! You did not pass the quiz. You cannot retake this assessment.
                            @endif
                        </h4>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Score Summary:</h5>
                        <ul>
                            <li>Your Score: {{ $attempt->score }}</li>
                            <li>Passing Score: {{ $quiz->passing_score }}</li>
                            <li>Total Possible: {{ $quiz->questions->sum('points') }}</li>
                            <li>Completion Time: {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} minutes</li>
                        </ul>
                    </div>
                    
                    @if ($attempt->status === 'passed')
                        <p>You have successfully completed the quiz requirement. Your application is now being processed for the next stage.</p>
                    @else
                        <p>Unfortunately, you did not meet the passing requirements for this assessment.</p>
                    @endif
                    
                    <a href="{{ route('candidate.profile') }}" class="btn btn-primary">Return to Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection