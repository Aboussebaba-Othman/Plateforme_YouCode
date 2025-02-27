@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $quiz->title }} - Question {{ $attempt->current_question + 1 }} of {{ $quiz->questions->count() }}</span>
                    <div id="timer" class="badge bg-warning text-dark">Time Remaining: <span id="countdown"></span></div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('candidate.quiz.answer', $attempt->id) }}">
                        @csrf
                        
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <div class="mb-4">
                            <h5>{{ $question->content }}</h5>
                            
                            @foreach($question->answers as $answer)
                                <div class="form-check mt-3">
                                    <input class="form-check-input" 
                                           type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}" 
                                           name="answer{{ $question->type === 'multiple' ? '[]' : '' }}" 
                                           id="answer_{{ $answer->id }}" 
                                           value="{{ $answer->id }}"
                                           >
                                    <label class="form-check-label" for="answer_{{ $answer->id }}">
                                        {{ $answer->content }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="progress mb-4">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ ($attempt->current_question / $quiz->questions->count()) * 100 }}%" 
                                 aria-valuenow="{{ $attempt->current_question }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $quiz->questions->count() }}">
                                {{ $attempt->current_question }} / {{ $quiz->questions->count() }}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Next Question</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('countdown');
        let timeRemaining = {{ $attempt->remaining_time }};
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                document.querySelector('form').submit();
                return;
            }
            
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            timeRemaining--;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    });
</script>
@endpush
@endsection