<!-- filepath: /C:/Users/Youcode/Desktop/Plateforme_YouCode/resources/views/admin/quiz/questions/create.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Add Question to Quiz: {{ $quiz->title }}</h1>
    <form action="{{ route('admin.quiz.question.store', $quiz->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="content">Question</label>
            <input type="text" name="content" id="content" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="points">Points</label>
            <input type="number" name="points" id="points" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="single">Single</option>
                <option value="multiple">Multiple</option>
            </select>
        </div>
        <div class="form-group">
            <label for="answers">Answers</label>
            <div id="answers">
                <div class="form-row mb-2">
                    <div class="col">
                        <input type="text" name="answers[0][content]" class="form-control" placeholder="Answer content" required>
                    </div>
                    <div class="col-auto">
                        <input type="radio" name="correct_answer" value="0" required>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <input type="text" name="answers[1][content]" class="form-control" placeholder="Answer content" required>
                    </div>
                    <div class="col-auto">
                        <input type="radio" name="correct_answer" value="1" required>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <input type="text" name="answers[2][content]" class="form-control" placeholder="Answer content" required>
                    </div>
                    <div class="col-auto">
                        <input type="radio" name="correct_answer" value="2" required>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col">
                        <input type="text" name="answers[3][content]" class="form-control" placeholder="Answer content" required>
                    </div>
                    <div class="col-auto">
                        <input type="radio" name="correct_answer" value="3" required>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Add Question</button>
    </form>
@endsection