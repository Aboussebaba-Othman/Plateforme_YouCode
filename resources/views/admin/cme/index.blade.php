@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestion des Groupes CME</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.cme.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Groupe CME
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
            <h5 class="mb-0">Groupes CME à venir</h5>
        </div>
        <div class="card-body p-0">
            @if($upcomingGroups->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du groupe</th>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Examinateur</th>
                            <th>Candidats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingGroups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->formatted_date }}</td>
                            <td>{{ $group->formatted_session_time }}</td>
                            <td>{{ $group->staff->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $group->candidate_count }}/4</span>
                                @if($group->is_full)
                                    <span class="badge bg-success">Complet</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.cme.show', $group->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cme.edit', $group->id) }}"
                                        class="btn btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.cme.destroy', $group->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe CME? Cette action supprimera également tous les entretiens associés.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 text-center">
                <p class="text-muted mb-0">Aucun groupe CME à venir.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Groupes CME passés</h5>
        </div>
        <div class="card-body p-0">
            @if($pastGroups->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du groupe</th>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Examinateur</th>
                            <th>Candidats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastGroups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->formatted_date }}</td>
                            <td>{{ $group->formatted_session_time }}</td>
                            <td>{{ $group->staff->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $group->candidate_count }}/4</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.cme.show', $group->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 text-center">
                <p class="text-muted mb-0">Aucun groupe CME passé.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection