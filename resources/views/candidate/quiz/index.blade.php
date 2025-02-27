@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Quiz Information</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h2>{{ $quiz->title }}</h2>
                    <p>{{ $quiz->description }}</p>

                    <div class="alert alert-info mb-4">
                        <h5>Important Information:</h5>
                        <ul class="mb-0">
                            <li>Time Limit: {{ $quiz->time_limit }} minutes</li>
                            <li>Passing Score: {{ $quiz->passing_score }}</li>
                            <li>Total Questions: {{ $quiz->questions->count() }}</li>
                        </ul>
                    </div>

                    @if ($activeAttempt)
                        <p>You have an ongoing quiz attempt.</p>
                        <a href="{{ route('candidate.quiz.take', $quiz->id) }}" class="btn btn-warning">Continue Quiz</a>
                    @else
                        <p>Once you start the quiz, you will have {{ $quiz->time_limit }} minutes to complete it.</p>
                        <a href="{{ route('candidate.quiz.start', $quiz->id) }}" class="btn btn-primary">Start Quiz</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection