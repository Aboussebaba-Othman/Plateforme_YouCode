@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Admin Dashboard</h1>
                </div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <h3>Welcome, Admin!</h3>

                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Total Candidates</h5>
                                    <p class="card-text display-4">{{ $stats['total_candidates'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Documents</h5>
                                    <p class="card-text display-4">{{ $stats['pending_documents'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Documents Approved</h5>
                                    <p class="card-text display-4">{{ $stats['approved_documents'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-info text-white shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Quiz Passed</h5>
                                    <p class="card-text display-4">{{ $stats['quiz_passed'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-dark text-white shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Tests Scheduled</h5>
                                    <p class="card-text display-4">{{ $stats['test_scheduled'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Quick Actions</h4>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.candidates') }}" class="btn btn-primary">
                                <i class="fas fa-users me-1"></i> View All Candidates
                            </a>
                            <a href="{{ route('admin.quiz.index') }}" class="btn btn-secondary">
                                <i class="fas fa-clipboard-list me-1"></i> Manage Quizzes
                            </a>
                            <a href="{{ route('admin.quiz.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> Create New Quiz
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-info">
                                <i class="fas fa-chart-line me-1"></i> View Statistics
                            </a>
                            <a href="{{ route('admin.interviews.index') }}" class="btn btn-warning">
                                <i class="fas fa-calendar-alt me-1"></i> Gérer les Entretiens
                            </a>
                        </div>
                    </div>
                    
                    <!-- Ajout de la section CME et Feedback -->
                    <div class="mt-4">
                        <h4>Gestion des entretiens</h4>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.interviews.index') }}" class="btn btn-warning">
                                <i class="fas fa-calendar-alt me-1"></i> Tous les Entretiens
                            </a>
                            <a href="{{ route('admin.cme.index') }}" class="btn btn-info">
                                <i class="fas fa-users-class me-1"></i> Groupes CME
                            </a>
                            <a href="{{ route('admin.interviews.create') }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-1"></i> Planifier un Entretien
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection