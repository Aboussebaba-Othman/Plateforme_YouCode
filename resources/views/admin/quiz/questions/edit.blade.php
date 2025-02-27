<!-- filepath: /C:/Users/Youcode/Desktop/Plateforme_YouCode/resources/views/admin/quiz/questions/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Edit Question</h1>
    <form action="{{ route('admin.quiz.question.update', ['quizId' => $quizId, 'questionId' => $question->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="question_text">Question</label>
            <input type="text" name="question_text" id="question_text" class="form-control" value="{{ $question->question_text }}" required>
        </div>
        <div class="form-group">
            <label for="points">Points</label>
            <input type="number" name="points" id="points" class="form-control" value="{{ $question->points }}" required>
        </div>
        <div class="form-group">
            <label for="answers">Answers</label>
            <div id="answers">
                @foreach($question->answers as $key => $answer)
                    <div class="form-row mb-2">
                        <div class="col">
                            <input type="text" name="answers[{{ $key }}][text]" class="form-control" value="{{ $answer->text }}" required>
                        </div>
                        <div class="col-auto">
                            <input type="radio" name="correct_answer" value="{{ $key }}" {{ $answer->is_correct ? 'checked' : '' }} required>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="btn btn-success">Update Question</button>
    </form>
@endsection