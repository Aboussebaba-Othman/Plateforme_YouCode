@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Détails de l'Entretien</h1>
            <h5 class="text-muted">Candidat: {{ $interview->candidate->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('staff.interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations sur l'entretien</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Type:</div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $interview->interview_type == 'technical' ? 'primary' : ($interview->interview_type == 'administrative' ? 'warning' : 'info') }}">
                                {{ ucfirst($interview->type_label) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date:</div>
                        <div class="col-md-8">{{ $interview->formatted_date }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Heure:</div>
                        <div class="col-md-8">{{ $interview->formatted_time }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Lieu:</div>
                        <div class="col-md-8">{{ $interview->location }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Statut:</div>
                        <div class="col-md-8">
                            {!! $interview->status_label !!}
                        </div>
                    </div>
                    @if($interview->notes)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Notes:</div>
                        <div class="col-md-8">{{ $interview->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($feedback)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Votre Feedback</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Évaluation:</div>
                        <div class="col-md-8">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $feedback->rating)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-muted"></i>
                                @endif
                            @endfor
                            ({{ $feedback->rating }}/5)
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Recommandation:</div>
                        <div class="col-md-8">
                            @if($feedback->recommendation == 'hire')
                                <span class="text-success"><i class="fas fa-check-circle"></i> Recruter</span>
                            @elseif($feedback->recommendation == 'consider')
                                <span class="text-warning"><i class="fas fa-question-circle"></i> À considérer</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle"></i> Rejeter</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Commentaires:</div>
                        <div class="col-md-8">{{ $feedback->comments }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date du feedback:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($feedback->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.feedback.edit', $feedback->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier mon feedback
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informations du candidat</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($interview->candidate->name) }}&background=random" 
                            alt="{{ $interview->candidate->name }}" class="rounded-circle" width="80">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nom:</div>
                        <div class="col-md-8">{{ $interview->candidate->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">{{ $interview->candidate->email }}</div>
                    </div>
                </div>
            </div>
            
            @if($interview->status === 'scheduled')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('staff.interviews.mark-completed', $interview->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2"
                                onclick="return confirm('Êtes-vous sûr de vouloir marquer cet entretien comme terminé?')">
                                <i class="fas fa-check"></i> Marquer comme terminé
                            </button>
                        </form>
                        
                        <form action="{{ route('staff.interviews.mark-no-show', $interview->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100"
                                onclick="return confirm('Êtes-vous sûr de vouloir marquer ce candidat comme absent?')">
                                <i class="fas fa-user-slash"></i> Marquer comme absence
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            
            @if(!$feedback && $interview->status !== 'scheduled')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ajouter un Feedback</h5>
                </div>
                <div class="card-body">
                    <p>Cet entretien nécessite votre évaluation.</p>
                    <div class="d-grid">
                        <a href="{{ route('admin.feedback.create', $interview->id) }}" class="btn btn-primary">
                            <i class="fas fa-comment"></i> Soumettre un feedback
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection