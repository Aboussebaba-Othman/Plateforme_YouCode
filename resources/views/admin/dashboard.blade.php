<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3>Welcome, Admin!</h3>
                    
                    <!-- Dashboard Stats -->
                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Candidates</h5>
                                    <p class="card-text display-4">{{ $stats['total_candidates'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Documents</h5>
                                    <p class="card-text display-4">{{ $stats['pending_documents'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Documents Approved</h5>
                                    <p class="card-text display-4">{{ $stats['approved_documents'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Quiz Passed</h5>
                                    <p class="card-text display-4">{{ $stats['quiz_passed'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tests Scheduled</h5>
                                    <p class="card-text display-4">{{ $stats['test_scheduled'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4">
                        <h4>Quick Actions</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.candidates') }}" class="btn btn-primary">View All Candidates</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection