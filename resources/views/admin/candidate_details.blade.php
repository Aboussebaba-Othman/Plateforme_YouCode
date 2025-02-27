<!-- resources/views/admin/candidate_details.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Candidate Details</span>
                    <a href="{{ route('admin.candidates') }}" class="btn btn-sm btn-secondary">Back to Candidates</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h4>User Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Username</th>
                                    <td>{{ $candidate->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $candidate->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Verified</th>
                                    <td>
                                        @if($candidate->user->email_verified_at)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Registered</th>
                                    <td>{{ $candidate->user->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4>Status Management</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Current Status: 
                                        @if($candidate->status == 'pending')
                                            <span class="badge bg-secondary">Pending</span>
                                        @elseif($candidate->status == 'documents_submitted')
                                            <span class="badge bg-warning">Documents Submitted</span>
                                        @elseif($candidate->status == 'documents_approved')
                                            <span class="badge bg-success">Documents Approved</span>
                                        @elseif($candidate->status == 'quiz_passed')
                                            <span class="badge bg-info">Quiz Passed</span>
                                        @elseif($candidate->status == 'test_scheduled')
                                            <span class="badge bg-primary">Test Scheduled</span>
                                        @endif
                                    </h5>
                                    
                                    <form action="{{ route('admin.candidate.update.status', $candidate->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Update Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="pending" {{ $candidate->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="documents_submitted" {{ $candidate->status == 'documents_submitted' ? 'selected' : '' }}>Documents Submitted</option>
                                                <option value="documents_approved" {{ $candidate->status == 'documents_approved' ? 'selected' : '' }}>Documents Approved</option>
                                                <option value="quiz_passed" {{ $candidate->status == 'quiz_passed' ? 'selected' : '' }}>Quiz Passed</option>
                                                <option value="test_scheduled" {{ $candidate->status == 'test_scheduled' ? 'selected' : '' }}>Test Scheduled</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Personal Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Full Name</th>
                                    <td>
                                        @if($candidate->first_name && $candidate->last_name)
                                            {{ $candidate->first_name }} {{ $candidate->last_name }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>
                                        @if($candidate->date_of_birth)
                                            {{ $candidate->date_of_birth->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $candidate->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $candidate->address ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card</th>
                                    <td>
                                        @if($candidate->id_card_path)
                                            <a href="{{ Storage::url($candidate->id_card_path) }}" target="_blank" class="btn btn-info">View ID Card</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection