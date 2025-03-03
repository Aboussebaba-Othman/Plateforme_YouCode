<!-- filepath: /C:/Users/Youcode/Desktop/Plateforme_YouCode/resources/views/admin/quiz/create.blade.php -->
@extends('layouts.app')

@section('content')
    
<div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-primary border-start border-primary ps-3">Créer un nouveau quiz</h1>
                        <a href="{{ route('admin.quiz.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux quiz
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.quiz.store') }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center mb-3">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-book"></i>
                                </span>
                                <h5 class="mb-0">Informations générales</h5>
                            </div>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Titre du quiz</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-heading"></i>
                                    </span>
                                    <input type="text" name="title" id="title" class="form-control" required placeholder="Entrez le titre du quiz...">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-align-left"></i>
                                    </span>
                                    <textarea name="description" id="description" class="form-control" rows="4" required placeholder="Décrivez le contenu et l'objectif de ce quiz..."></textarea>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3 mt-4">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-cog"></i>
                                </span>
                                <h5 class="mb-0">Paramètres du quiz</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="time_limit" class="form-label fw-bold">Limite de temps (minutes)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning text-white">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <input type="number" min="1" name="time_limit" id="time_limit" class="form-control" required value="30">
                                        </div>
                                        <small class="text-muted">Durée maximale pour compléter le quiz</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passing_score" class="form-label fw-bold">Score de réussite (%)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-info text-white">
                                                <i class="fas fa-percent"></i>
                                            </span>
                                            <input type="number" min="1" max="100" name="passing_score" id="passing_score" class="form-control" required value="70">
                                        </div>
                                        <small class="text-muted">Pourcentage minimal pour réussir le quiz</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3 mb-4">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">Publier immédiatement ce quiz</label>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.quiz.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Créer le quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Conseils pour créer un bon quiz</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Après avoir créé votre quiz, vous pourrez ajouter des questions et des réponses.
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>Assurez-vous que le titre est clair et représentatif du contenu</div>
                            </li>
                            <li class="list-group-item d-flex">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>Fournissez une description détaillée pour aider les participants à comprendre l'objectif</div>
                            </li>
                            <li class="list-group-item d-flex">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>Définissez un temps limite raisonnable en fonction de la complexité des questions</div>
                            </li>
                            <li class="list-group-item d-flex">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                <div>Le score de réussite standard est généralement de 70% à 80%</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection