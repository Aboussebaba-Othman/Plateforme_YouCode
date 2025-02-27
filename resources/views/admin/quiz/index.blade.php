@extends('layouts.app')

@section('content')
    <h1>Quiz Management</h1>
    <a href="{{ route('admin.quiz.create') }}" class="btn btn-primary">Create New Quiz</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Questions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quizzes as $quiz)
                <tr>
                    <td>{{ $quiz->id }}</td>
                    <td>{{ $quiz->title }}</td>
                    <td>{{ $quiz->questions_count }}</td>
                    <td>
                        <a href="{{ route('admin.quiz.edit', $quiz->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $quizzes->links() }}
@endsection