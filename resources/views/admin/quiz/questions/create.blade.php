@extends('layouts.app')

@section('content')
<div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-gray-800 question-header">Ajouter une question au quiz: {{ $quiz->title }}</h1>
                        <a href="{{ route('admin.quiz.index', $quiz->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au quiz
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.quiz.question.store', $quiz->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="content" class="form-label fw-bold">Question</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-question-circle"></i>
                                    </span>
                                    <input type="text" name="content" id="content" class="form-control" required placeholder="Entrez votre question ici...">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="points" class="form-label fw-bold">Points</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning text-white">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            <input type="number" min="1" name="points" id="points" class="form-control" required value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type" class="form-label fw-bold">Type de question</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-info text-white">
                                                <i class="fas fa-list-check"></i>
                                            </span>
                                            <select name="type" id="type" class="form-select" required>
                                                <option value="single">Réponse unique</option>
                                                <option value="multiple">Réponses multiples</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <label class="form-label fw-bold mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Réponses
                                </label>
                                
                                <div id="answers">
                                    <div class="answer-container">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <input type="text" name="answers[0][content]" class="form-control" placeholder="Réponse 1" required>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-indicator" type="radio" name="correct_answer" value="0" id="correct0" required>
                                                    <label class="form-check-label" for="correct0">Correcte</label>
                                                    <div class="correct-badge">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="answer-container">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <input type="text" name="answers[1][content]" class="form-control" placeholder="Réponse 2" required>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-indicator" type="radio" name="correct_answer" value="1" id="correct1" required>
                                                    <label class="form-check-label" for="correct1">Correcte</label>
                                                    <div class="correct-badge">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="answer-container">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <input type="text" name="answers[2][content]" class="form-control" placeholder="Réponse 3" required>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-indicator" type="radio" name="correct_answer" value="2" id="correct2" required>
                                                    <label class="form-check-label" for="correct2">Correcte</label>
                                                    <div class="correct-badge">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="answer-container">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <input type="text" name="answers[3][content]" class="form-control" placeholder="Réponse 4" required>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input correct-indicator" type="radio" name="correct_answer" value="3" id="correct3" required>
                                                    <label class="form-check-label" for="correct3">Correcte</label>
                                                    <div class="correct-badge">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-3 add-answer-btn" id="add-answer-btn">
                                        <i class="fas fa-plus-circle me-2"></i> Ajouter une réponse
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('admin.quiz.index', $quiz->id) }}" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Ajouter la question
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle between single and multiple answer types
            const typeSelect = document.getElementById('type');
            typeSelect.addEventListener('change', function() {
                const answerContainers = document.querySelectorAll('.correct-indicator');
                if (this.value === 'multiple') {
                    answerContainers.forEach(input => {
                        input.type = 'checkbox';
                        input.name = 'correct_answers[]';
                    });
                } else {
                    answerContainers.forEach(input => {
                        input.type = 'radio';
                        input.name = 'correct_answer';
                    });
                }
            });
            
            // Add new answer option
            let answerCount = 4;
            const addAnswerBtn = document.getElementById('add-answer-btn');
            addAnswerBtn.addEventListener('click', function() {
                const answersContainer = document.getElementById('answers');
                const newAnswerDiv = document.createElement('div');
                newAnswerDiv.className = 'answer-container';
                
                const isMultiple = typeSelect.value === 'multiple';
                const inputType = isMultiple ? 'checkbox' : 'radio';
                const inputName = isMultiple ? 'correct_answers[]' : 'correct_answer';
                
                newAnswerDiv.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col">
                            <input type="text" name="answers[${answerCount}][content]" class="form-control" placeholder="Réponse ${answerCount + 1}" required>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input correct-indicator" type="${inputType}" name="${inputName}" value="${answerCount}" id="correct${answerCount}" required>
                                <label class="form-check-label" for="correct${answerCount}">Correcte</label>
                                <div class="correct-badge">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-answer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                // Insert before the "Add Answer" button
                answersContainer.insertBefore(newAnswerDiv, addAnswerBtn);
                answerCount++;
                
                // Add event listener to remove button
                const removeBtn = newAnswerDiv.querySelector('.remove-answer');
                removeBtn.addEventListener('click', function() {
                    newAnswerDiv.remove();
                });
            });
        });
    </script>
@endsection