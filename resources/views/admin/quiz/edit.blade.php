@extends('layouts.app')

@section('content')
    <h1>Edit Quiz</h1>
    <form action="{{ route('admin.quiz.update', $quiz->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $quiz->title }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" required>{{ $quiz->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="time_limit">Time Limit (minutes)</label>
            <input type="number" name="time_limit" id="time_limit" class="form-control" value="{{ $quiz->time_limit }}" required>
        </div>
        <div class="form-group">
            <label for="passing_score">Passing Score</label>
            <input type="number" name="passing_score" id="passing_score" class="form-control" value="{{ $quiz->passing_score }}" required>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $quiz->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>

    <h2 class="mt-4">Questions</h2>
    <a href="{{ route('admin.quiz.question.create', $quiz->id) }}" class="btn btn-primary">Add Question</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quiz->questions as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ $question->question_text }}</td>
                    <td>
                        <a href="{{ route('admin.quiz.question.edit', ['quizId' => $quiz->id, 'questionId' => $question->id]) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.quiz.question.destroy', ['quizId' => $quiz->id, 'questionId' => $question->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection