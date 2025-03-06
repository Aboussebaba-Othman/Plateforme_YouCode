@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestion des Entretiens</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.interviews.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvel Entretien
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
                            <th>Examinateur</th>
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
                            <td>{{ $interview->staff->name }}</td>
                            <td>{{ $interview->location }}</td>
                            <td>{!! $interview->status_label !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.interviews.edit', $interview->id) }}"
                                        class="btn btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-primary send-invitation"
                                        data-id="{{ $interview->id }}" title="Envoyer l'invitation">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <form action="{{ route('admin.interviews.destroy', $interview->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet entretien?')">
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
                            <th>Examinateur</th>
                            <th>Statut</th>
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
                            <td>{{ $interview->staff->name }}</td>
                            <td>{!! $interview->status_label !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-info"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.interviews.edit', $interview->id) }}"
                                        class="btn btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
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
                <p class="text-muted mb-0">Aucun entretien passé.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Send invitation buttons
    const sendInvitationButtons = document.querySelectorAll('.send-invitation');

    sendInvitationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const interviewId = this.dataset.id;

            // Change button state to loading
            this.disabled = true;
            this.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            // Send AJAX request
            fetch(`/admin/interviews/${interviewId}/send-invitation`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Invitation envoyée avec succès.');
                    } else {
                        alert('Une erreur s\'est produite. Veuillez réessayer.');
                    }

                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-envelope"></i>';
                });
        });
    });
});
</script>
@endsection