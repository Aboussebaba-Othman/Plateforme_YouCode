@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Candidate Profile</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3>Welcome, {{ Auth::user()->name }}</h3>
                    
                    <div class="my-4">
                        <h4>Your Application Status</h4>
                        @if (!Auth::user()->candidate || Auth::user()->candidate->status === 'pending')
                            <div class="alert alert-warning">
                                Please submit your documents to proceed with the application.
                                <a href="{{ route('candidate.documents') }}" class="btn btn-primary mt-2">Submit Documents</a>
                            </div>
                        @else
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Document Submission
                                    @if (in_array(Auth::user()->candidate->status, ['documents_submitted', 'documents_approved', 'quiz_passed', 'test_scheduled']))
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Document Approval
                                    @if (in_array(Auth::user()->candidate->status, ['documents_approved', 'quiz_passed', 'test_scheduled']))
                                        <span class="badge bg-success">Approved</span>
                                    @elseif (Auth::user()->candidate->status === 'documents_submitted')
                                        <span class="badge bg-info">Under Review</span>
                                    @else
                                        <span class="badge bg-secondary">Not Started</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Quiz Completion
                                    @if (in_array(Auth::user()->candidate->status, ['quiz_passed', 'test_scheduled']))
                                        <span class="badge bg-success">Passed</span>
                                    @elseif (Auth::user()->candidate->status === 'documents_approved')
                                        <span class="badge bg-warning">Ready to Take</span>
                                    @else
                                        <span class="badge bg-secondary">Not Available</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Test Scheduling
                                    @if (Auth::user()->candidate->status === 'test_scheduled')
                                        <span class="badge bg-success">Scheduled</span>
                                    @elseif (Auth::user()->candidate->status === 'quiz_passed')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">Not Available</span>
                                    @endif
                                </li>
                            </ul>
                        @endif
                    </div>

                    @if (Auth::user()->candidate)
                        <div class="my-4">
                            <h4>Personal Information</h4>
                            <table class="table">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ Auth::user()->candidate->first_name }} {{ Auth::user()->candidate->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td>{{ Auth::user()->candidate->date_of_birth ? Auth::user()->candidate->date_of_birth->format('M d, Y') : 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ Auth::user()->candidate->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ Auth::user()->candidate->address ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card:</th>
                                    <td>
                                        @if (Auth::user()->candidate->id_card_path)
                                            <a href="{{ Storage::url(Auth::user()->candidate->id_card_path) }}" target="_blank">View ID</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <a href="{{ route('candidate.documents') }}" class="btn btn-primary">Update Information</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection