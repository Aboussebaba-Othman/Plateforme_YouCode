<!-- filepath: /C:/Users/Youcode/Desktop/Plateforme_YouCode/resources/views/admin/quiz/create.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Create New Quiz</h1>
    <form action="{{ route('admin.quiz.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="time_limit">Time Limit (minutes)</label>
            <input type="number" name="time_limit" id="time_limit" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="passing_score">Passing Score</label>
            <input type="number" name="passing_score" id="passing_score" class="form-control" required>
        </div>
        <!-- <div class="form-group form-check">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input">
            <label for="is_active" class="form-check-label">Active</label>
        </div> -->
        <button type="submit" class="btn btn-success">Create</button>
    </form>
@endsection