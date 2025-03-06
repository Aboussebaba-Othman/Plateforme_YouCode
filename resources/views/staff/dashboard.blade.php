@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Tableau de Bord</h1>
            <h5 class="text-muted">Bienvenue, {{ Auth::user()->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('staff.interviews.index') }}" class="btn btn-primary">
                <i class="fas fa-calendar-alt"></i> Tous mes entretiens
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <!-- Statistiques -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Entretiens</h6>
                                    <h2 class="mb-0">{{ $stats['total_interviews'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">À Venir</h6>
                                    <h2 class="mb-0">{{ $stats['upcoming_interviews'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Terminés</h6>
                                    <h2 class="mb-0">{{ $stats['completed_interviews'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Feedback en attente</h6>
                                    <h2 class="mb-0">{{ $stats['pending_feedback'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entretiens à venir -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Prochains Entretiens</h5>
                </div>
                <div class="card-body p-0">
                    @if($upcomingInterviews->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingInterviews as $interview)
                        <a href="{{ route('staff.interviews.show', $interview->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $interview->candidate->name }}</h6>
                                <span class="badge bg-{{ $interview->interview_type == 'technical' ? 'primary' : ($interview->interview_type == 'administrative' ? 'warning' : 'info') }}">
                                    {{ ucfirst($interview->type_label) }}
                                </span>
                            </div>
                            <p class="mb-1">
                                <i class="fas fa-calendar-day"></i> {{ $interview->formatted_date }} - 
                                <i class="fas fa-clock"></i> {{ $interview->formatted_time }}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $interview->location }}
                            </small>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 text-center">
                        <p class="text-muted mb-0">Aucun entretien planifié prochainement.</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('staff.interviews.index') }}" class="btn btn-sm btn-primary">
                        Voir tous les entretiens
                    </a>
                </div>
            </div>
        </div>

        <!-- Feedbacks en attente -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Feedbacks en Attente</h5>
                </div>
                <div class="card-body p-0">
                    @if($interviewsWithoutFeedback->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($interviewsWithoutFeedback as $interview)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $interview->candidate->name }}</h6>
                                <span class="badge bg-{{ $interview->interview_type == 'technical' ? 'primary' : ($interview->interview_type == 'administrative' ? 'warning' : 'info') }}">
                                    {{ ucfirst($interview->type_label) }}
                                </span>
                            </div>
                            <p class="mb-1">
                                <i class="fas fa-calendar-day"></i> {{ $interview->formatted_date }} - 
                                <i class="fas fa-clock"></i> {{ $interview->formatted_time }}
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('admin.feedback.create', $interview->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-comment"></i> Ajouter un feedback
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 text-center">
                        <p class="text-muted mb-0">Aucun feedback en attente.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection