@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h3 class="mb-0">Candidate Profile</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h3 class="mb-4">Welcome, {{ Auth::user()->name }}</h3>
                    
                    <h4 class="mb-3">Your Application Status</h4>
                    @if (!Auth::user()->candidate || Auth::user()->candidate->status === 'pending')
                        <div class="alert alert-warning" role="alert">
                            <p>Please submit your documents to proceed with the application.</p>
                            <a href="{{ route('candidate.documents') }}" class="btn btn-primary">Submit Documents</a>
                        </div>
                    @else
                        <ul class="list-group mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Document Submission
                                <span class="badge bg-{{ in_array(Auth::user()->candidate->status, ['documents_submitted', 'documents_approved', 'quiz_passed', 'test_scheduled']) ? 'success' : 'warning' }}">{{ in_array(Auth::user()->candidate->status, ['documents_submitted', 'documents_approved', 'quiz_passed', 'test_scheduled']) ? 'Completed' : 'Pending' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Document Approval
                                <span class="badge bg-{{ Auth::user()->candidate->status === 'documents_approved' ? 'success' : (Auth::user()->candidate->status === 'documents_submitted' ? 'info' : 'secondary') }}">
                                    {{ Auth::user()->candidate->status === 'documents_approved' ? 'Approved' : (Auth::user()->candidate->status === 'documents_submitted' ? 'Under Review' : 'Not Started') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Quiz Completion
                                <span class="badge bg-{{ Auth::user()->candidate->status === 'quiz_passed' ? 'success' : (Auth::user()->candidate->status === 'documents_approved' ? 'warning' : 'secondary') }}">
                                    {{ Auth::user()->candidate->status === 'quiz_passed' ? 'Passed' : (Auth::user()->candidate->status === 'documents_approved' ? 'Ready to Take' : 'Not Available') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Test Scheduling
                                <span class="badge bg-{{ Auth::user()->candidate->status === 'test_scheduled' ? 'success' : (Auth::user()->candidate->status === 'quiz_passed' ? 'warning' : 'secondary') }}">
                                    {{ Auth::user()->candidate->status === 'test_scheduled' ? 'Scheduled' : (Auth::user()->candidate->status === 'quiz_passed' ? 'Pending' : 'Not Available') }}
                                </span>
                            </li>
                        </ul>
                    @endif

                    @if (Auth::user()->candidate)
                        <h4 class="mb-3">Personal Information</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ Auth::user()->candidate->first_name }} {{ Auth::user()->candidate->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ Auth::user()->candidate->date_of_birth ? Auth::user()->candidate->date_of_birth->format('M d, Y') : 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ Auth::user()->candidate->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ Auth::user()->candidate->address ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card</th>
                                    <td>
                                        @if (Auth::user()->candidate->id_card_path)
                                            <a href="{{ Storage::url(Auth::user()->candidate->id_card_path) }}" target="_blank">View ID</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <a href="{{ route('candidate.documents') }}" class="btn btn-primary">Update Information</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
