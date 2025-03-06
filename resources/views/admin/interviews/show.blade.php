@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Détails de l'Entretien</h1>
            @if($interview->candidate)
            <h5 class="text-muted">Candidat: {{ $interview->candidate->name }}</h5>
            @else
            <h5 class="text-muted">Candidat: [Supprimé]</h5>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

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
                        <div class="col-md-4 fw-bold">Examinateur:</div>
                        <div class="col-md-8">{{ $interview->staff ? $interview->staff->name : '[Examinateur non disponible]' }}</div>
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
        </div>

        <div class="col-md-4">
            @if($interview->candidate)
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
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.candidate.view', $interview->candidate->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user"></i> Voir le profil
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informations du candidat</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Le candidat associé à cet entretien n'existe plus.
                    </div>
                </div>
            </div>
            @endif
            
            @if($interview->status === 'scheduled')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.interviews.update', $interview->id) }}" method="POST" id="updateStatusForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="staff_id" value="{{ $interview->staff_id }}">
                        <input type="hidden" name="interview_date" value="{{ $interview->interview_date->format('Y-m-d') }}">
                        <input type="hidden" name="interview_time" value="{{ $interview->interview_time }}">
                        <input type="hidden" name="location" value="{{ $interview->location }}">
                        <input type="hidden" name="interview_type" value="{{ $interview->interview_type }}">
                        <input type="hidden" name="notes" value="{{ $interview->notes }}">
                        <input type="hidden" name="status" id="statusInput" value="">
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success mark-completed">
                                <i class="fas fa-check"></i> Marqué comme terminé
                            </button>
                            <button type="button" class="btn btn-danger mark-cancelled">
                                <i class="fas fa-times"></i> Annuler l'entretien
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateStatusForm');
    const statusInput = document.getElementById('statusInput');
    
    document.querySelector('.mark-completed').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir marquer cet entretien comme terminé ?')) {
            statusInput.value = 'completed';
            form.submit();
        }
    });
    
    document.querySelector('.mark-cancelled').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir annuler cet entretien ?')) {
            statusInput.value = 'cancelled';
            form.submit();
        }
    });
});
</script>
@endsection