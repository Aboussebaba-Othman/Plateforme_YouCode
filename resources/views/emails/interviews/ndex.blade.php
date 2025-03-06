@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mes Entretiens</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Entretiens à venir</h5>
        </div>
        <div class="card-body p-0">
            @if($upcomingInterviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Candidat</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Lieu</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingInterviews as $interview)
                        <tr>
                            <td>{{ $interview->candidate->name }}</td>
                            <td>{{ $interview->type_label }}</td>
                            <td>{{ $interview->formatted_date }}</td>
                            <td>{{ $interview->formatted_time }}</td>
                            <td>{{ $interview->location }}</td>
                            <td>{!! $interview->status_label !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.interviews.show', $interview->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($interview->status == 'scheduled')
                                    <form action="{{ route('staff.interviews.mark-completed', $interview->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="Marquer comme terminé"
                                            onclick="return confirm('Êtes-vous sûr de vouloir marquer cet entretien comme terminé?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('staff.interviews.mark-no-show', $interview->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning" title="Marquer comme absence"
                                            onclick="return confirm('Êtes-vous sûr de vouloir marquer ce candidat comme absent?')">
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
                <p class="text-muted mb-0">Aucun entretien à venir.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Entretiens passés</h5>
        </div>
        <div class="card-body p-0">
            @if($pastInterviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Candidat</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Statut</th>
                            <th>Feedback</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastInterviews as $interview)
                        <tr>
                            <td>{{ $interview->candidate->name }}</td>
                            <td>{{ $interview->type_label }}</td>
                            <td>{{ $interview->formatted_date }}</td>
                            <td>{{ $interview->formatted_time }}</td>
                            <td>{!! $interview->status_label !!}</td>
                            <td>
                                @if($interview->feedbacks->count() > 0)
                                    <span class="badge bg-success">Feedback soumis</span>
                                @else
                                    <span class="badge bg-warning">Feedback en attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.interviews.show', $interview->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($interview->feedbacks->count() == 0)
                                    <a href="{{ route('admin.feedback.create', $interview->id) }}" class="btn btn-success"
                                        title="Ajouter un feedback">
                                        <i class="fas fa-comment"></i>
                                    </a>
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
                <p class="text-muted mb-0">Aucun entretien passé.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection