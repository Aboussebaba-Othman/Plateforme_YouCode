@extends('layouts.app')

@section('content')
<div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-primary border-start border-primary ps-3">Gestion des Quiz</h1>
                    <a href="{{ route('admin.quiz.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Créer un nouveau quiz
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Titre</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td class="text-center">{{ $quiz->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $quiz->title }}</div>
                                        <small class="text-muted">Créé le {{ $quiz->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">{{ $quiz->questions_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(isset($quiz->is_published) && $quiz->is_published)
                                            <span class="badge bg-success">Publié</span>
                                        @else
                                            <span class="badge bg-secondary">Brouillon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.quiz.index', $quiz->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quiz.edit', $quiz->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.quiz.question.create', $quiz->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-question-circle"></i>
                                            </a>
                                            <form action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            
                            @if(count($quizzes) == 0)
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fs-1 mb-3"></i>
                                            <p>Aucun quiz n'a été créé pour le moment</p>
                                            <a href="{{ route('admin.quiz.create') }}" class="btn btn-primary mt-2">
                                                Créer votre premier quiz
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $quizzes->links() }}
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-list"></i>
                            </div>
                            <span>Total des quiz: <strong>{{ $quizzes->total() }}</strong></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.quiz.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Nouveau Quiz
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Quizzes actifs: <strong>{{ isset($activeQuizzes) ? $activeQuizzes : 0 }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $quizzes->links() }}
@endsection