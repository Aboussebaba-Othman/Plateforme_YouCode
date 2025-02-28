@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">Edit Quiz</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quiz.update', $quiz->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ $quiz->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" required>{{ $quiz->description }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" name="time_limit" id="time_limit" class="form-control" value="{{ $quiz->time_limit }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="passing_score" class="form-label">Passing Score</label>
                                <input type="number" name="passing_score" id="passing_score" class="form-control" value="{{ $quiz->passing_score }}" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $quiz->is_active ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Update Quiz
                        </button>
                        <a href="{{ route('admin.quiz.index') }}" class="btn btn-secondary ms-2">
                            <i class="bi bi-arrow-left me-1"></i> Back to Quizzes
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Questions</h2>
                    <a href="{{ route('admin.quiz.question.create', $quiz->id) }}" class="btn btn-light">
                        <i class="bi bi-plus-circle me-1"></i> Add Question
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($quiz->questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width: 80px">ID</th>
                                        <th>Question</th>
                                        <th class="text-end pe-3" style="width: 200px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quiz->questions as $question)
                                        <tr>
                                            <td class="ps-3">{{ $question->id }}</td>
                                            <td>{{ $question->content }}</td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.quiz.question.edit', ['quizId' => $quiz->id, 'questionId' => $question->id]) }}" class="btn btn-warning">
                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.quiz.question.destroy', ['quizId' => $quiz->id, 'questionId' => $question->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">
                                                            <i class="bi bi-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="bi bi-question-circle" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="h5">No questions yet</h3>
                            <p>Get started by adding your first question to this quiz.</p>
                            <a href="{{ route('admin.quiz.question.create', $quiz->id) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add First Question
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection