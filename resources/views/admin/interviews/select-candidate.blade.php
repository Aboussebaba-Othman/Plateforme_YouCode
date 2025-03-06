@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Sélectionner un Candidat</h1>
            <p class="text-muted">Choisissez un candidat pour l'entretien</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Candidats</h5>
        </div>
        <div class="card-body p-0">
            @if($candidates->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($candidates as $candidate)
                        <tr>
                            <td>{{ $candidate->name }}</td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.interviews.create', ['candidate_id' => $candidate->id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-calendar-plus"></i> Planifier un entretien
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 text-center">
                <p class="text-muted mb-0">Aucun candidat disponible.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection