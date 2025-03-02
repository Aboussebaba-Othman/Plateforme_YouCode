@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Edit Question</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quiz.question.update', ['quizId' => $quizId, 'questionId' => $question->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Question</label>
                            <input type="text" name="content" id="content" class="form-control" value="{{ $question->content }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="points" class="form-label fw-bold">Points</label>
                            <input type="number" name="points" id="points" class="form-control" value="{{ $question->points }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="single" {{ $question->type == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="multiple" {{ $question->type == 'multiple' ? 'selected' : '' }}>Multiple</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="answers" class="form-label fw-bold">Answers</label>
                            <div id="answers">
                                @foreach($question->answers as $key => $answer)
                                    <div class="input-group mb-2">
                                        <input type="text" name="answers[{{ $key }}][content]" class="form-control" value="{{ $answer->content }}" required>
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_answer" value="{{ $key }}" {{ $answer->is_correct ? 'checked' : '' }} required>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Update Question
                            </button>
                            <a href="{{ route('admin.quiz.show', $quizId) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Quiz
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection