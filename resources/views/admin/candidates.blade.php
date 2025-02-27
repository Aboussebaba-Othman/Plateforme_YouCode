<!-- resources/views/admin/candidates.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Candidates Management</span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Back to Dashboard</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count($candidates) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($candidates as $candidate)
                                        <tr>
                                            <td>{{ $candidate->id }}</td>
                                            <td>{{ $candidate->user->name }}</td>
                                            <td>
                                                @if($candidate->first_name && $candidate->last_name)
                                                    {{ $candidate->first_name }} {{ $candidate->last_name }}
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($candidate->status == 'pending')
                                                    <span class="badge bg-secondary">Pending</span>
                                                @elseif($candidate->status == 'documents_submitted')
                                                    <span class="badge bg-warning">Docs Submitted</span>
                                                @elseif($candidate->status == 'documents_approved')
                                                    <span class="badge bg-success">Docs Approved</span>
                                                @elseif($candidate->status == 'quiz_passed')
                                                    <span class="badge bg-info">Quiz Passed</span>
                                                @elseif($candidate->status == 'test_scheduled')
                                                    <span class="badge bg-primary">Test Scheduled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($candidate->id_card_path)
                                                    <a href="{{ Storage::url($candidate->id_card_path) }}" target="_blank" class="btn btn-sm btn-info">View ID</a>
                                                @else
                                                    <span class="badge bg-danger">Not Uploaded</span>
                                                @endif
                                            </td>
                                            <td>{{ $candidate->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('admin.candidate.view', $candidate->id) }}" class="btn btn-sm btn-primary">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $candidates->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">No candidates found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
