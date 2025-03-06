@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Détails du Groupe CME</h1>
            <h5 class="text-muted">{{ $group->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.cme.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            @if($group->status !== 'completed')
            <a href="{{ route('admin.cme.edit', $group->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations du groupe</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Nom :</div>
                        <div class="col-md-7">{{ $group->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Date :</div>
                        <div class="col-md-7">{{ $group->formatted_date }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Session :</div>
                        <div class="col-md-7">{{ $group->formatted_session_time }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Statut :</div>
                        <div class="col-md-7">{!! $group->status_badge !!}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Examinateur :</div>
                        <div class="col-md-7">{{ $group->staff->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Lieu :</div>
                        <div class="col-md-7">{{ $group->interviews->first() ? $group->interviews->first()->location : 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Candidats :</div>
                        <div class="col-md-7">
                            <span class="badge bg-info">{{ $group->candidate_count }}/4</span>
                            @if($group->is_full)
                                <span class="badge bg-success">Complet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Candidats du groupe</h5>
                </div>
                <div class="card-body p-0">
                    @if($group->interviews->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Candidat</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Feedback</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->interviews as $interview)
                                <tr>
                                    <td>{{ $interview->candidate->name }}</td>
                                    <td>{{ $interview->candidate->email }}</td>
                                    <td>{!! $interview->status_label !!}</td>
                                    <td>
                                        @if($interview->has_feedback)
                                            <span class="badge bg-success">Soumis</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-info" title="Voir l'entretien">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$interview->has_feedback && $interview->status === 'completed')
                                            <a href="{{ route('admin.feedback.create', $interview->id) }}" class="btn btn-success" title="Ajouter un feedback">
                                                <i class="fas fa-comment"></i>
                                            </a>
                                            @endif
                                            @if($interview->status === 'scheduled')
                                            <form action="{{ route('admin.interviews.update.status', $interview->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-primary" title="Marquer comme terminé">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.interviews.update.status', $interview->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="no_show">
                                                <button type="submit" class="btn btn-warning" title="Marquer comme absent">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-4 text-center">
                        <p class="text-muted mb-0">Aucun candidat assigné à ce groupe.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection