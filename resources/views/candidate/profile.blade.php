@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Candidate Profile</h3>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    @if (session('success'))
                        <div class="rounded-md bg-green-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h3 class="text-xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</h3>
                    
                    <div class="mt-8 mb-8">
                        <h4 class="text-lg font-medium text-gray-700 mb-3">Your Application Status</h4>
                        @if (!Auth::user()->candidate || Auth::user()->candidate->status === 'pending')
                            <div class="rounded-md bg-yellow-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">Please submit your documents to proceed with the application.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('candidate.documents') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                                                Submit Documents
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-hidden bg-white shadow sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    <li>
                                        <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                            <div class="text-sm font-medium text-gray-900">Document Submission</div>
                                            @if (in_array(Auth::user()->candidate->status, ['documents_submitted', 'documents_approved', 'quiz_passed', 'test_scheduled']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li>
                                        <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                            <div class="text-sm font-medium text-gray-900">Document Approval</div>
                                            @if (in_array(Auth::user()->candidate->status, ['documents_approved', 'quiz_passed', 'test_scheduled']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                            @elseif (Auth::user()->candidate->status === 'documents_submitted')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Under Review</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Not Started</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li>
                                        <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                            <div class="text-sm font-medium text-gray-900">Quiz Completion</div>
                                            @if (in_array(Auth::user()->candidate->status, ['quiz_passed', 'test_scheduled']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Passed</span>
                                            @elseif (Auth::user()->candidate->status === 'documents_approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ready to Take</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Not Available</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li>
                                        <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                            <div class="text-sm font-medium text-gray-900">Test Scheduling</div>
                                            @if (Auth::user()->candidate->status === 'test_scheduled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Scheduled</span>
                                            @elseif (Auth::user()->candidate->status === 'quiz_passed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Not Available</span>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>

                    @if (Auth::user()->candidate)
                        <div class="mt-8 mb-8">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Personal Information</h4>
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <div class="border-t border-gray-200">
                                    <dl>
                                        <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ Auth::user()->candidate->first_name }} {{ Auth::user()->candidate->last_name }}</dd>
                                        </div>
                                        <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ Auth::user()->candidate->date_of_birth ? Auth::user()->candidate->date_of_birth->format('M d, Y') : 'Not provided' }}</dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ Auth::user()->candidate->phone ?? 'Not provided' }}</dd>
                                        </div>
                                        <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ Auth::user()->candidate->address ?? 'Not provided' }}</dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">ID Card</dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                @if (Auth::user()->candidate->id_card_path)
                                                    <a href="{{ Storage::url(Auth::user()->candidate->id_card_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View ID</a>
                                                @else
                                                    Not uploaded
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('candidate.documents') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                                    Update Information
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection