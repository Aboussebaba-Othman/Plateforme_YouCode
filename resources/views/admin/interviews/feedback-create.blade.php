@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Ajouter un Feedback d'Entretien</h1>
            <h5 class="text-muted">
                Candidat: {{ $interview->candidate->name }} | 
                Type: {{ ucfirst($interview->interview_type) }} | 
                Date: {{ \Carbon\Carbon::parse($interview->interview_date)->format('d/m/Y') }}
            </h5>
        </div>
        <div class="col-md-4 text-end">
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux détails
                </a>
            @else
                <a href="{{ route('staff.interviews.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Formulaire de Feedback</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.feedback.store', $interview->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="comments" class="form-label">Commentaires détaillés</label>
                    <textarea class="form-control" id="comments" name="comments" rows="6" required>{{ old('comments') }}</textarea>
                    @error('comments')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Évaluation globale</label>
                    <div class="d-flex">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" 
                                    value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                            </div>
                        @endfor
                    </div>
                    <small class="text-muted">1 = Insuffisant, 5 = Excellent</small>
                    @error('rating')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Recommandation</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recommendation" id="hire" 
                            value="hire" {{ old('recommendation') == 'hire' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="hire">
                            <span class="text-success"><i class="fas fa-check-circle"></i> Recruter</span> - Candidat idéal pour le poste
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recommendation" id="consider" 
                            value="consider" {{ old('recommendation') == 'consider' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="consider">
                            <span class="text-warning"><i class="fas fa-question-circle"></i> À considérer</span> - Potentiel mais quelques réserves
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recommendation" id="reject" 
                            value="reject" {{ old('recommendation') == 'reject' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="reject">
                            <span class="text-danger"><i class="fas fa-times-circle"></i> Rejeter</span> - Ne correspond pas aux critères
                        </label>
                    </div>
                    @error('recommendation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer le Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection