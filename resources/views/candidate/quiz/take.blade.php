@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $quiz->title }}</span>
                    <div id="timer" class="badge bg-warning text-dark">Time Remaining: <span id="countdown"></span></div>
                </div>

                <div class="card-body">
                    <form id="quizForm" method="POST" action="{{ route('candidate.quiz.submit', $attempt->id) }}">
                        @csrf

                        @foreach($quiz->questions as $index => $question)
                            <div class="mb-4">
                                <h5>Question {{ $index + 1 }}: {{ $question->content }}</h5>
                                
                                @foreach($question->answers as $answer)
                                    <div class="form-check">
                                        <input class="form-check-input" type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}" 
                                               name="question_{{ $question->id }}{{ $question->type === 'multiple' ? '[]' : '' }}" 
                                               id="answer_{{ $answer->id }}" 
                                               value="{{ $answer->id }}">
                                        <label class="form-check-label" for="answer_{{ $answer->id }}">
                                            {{ $answer->content }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary">Submit Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Timer countdown
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('countdown');
        let timeRemaining = {{ $attempt->remaining_time }};
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                document.getElementById('quizForm').submit();
                return;
            }
            
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            timeRemaining--;
        }
        
        // Update timer immediately and then every second
        updateTimer();
        setInterval(updateTimer, 1000);
    });
</script>
@endpush
@endsection