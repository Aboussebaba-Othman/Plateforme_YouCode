@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Submit Documents</h3>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <ul class="list-disc ml-5 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('candidate.submit.documents') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                value="{{ old('first_name', $candidate ? $candidate->first_name : '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                value="{{ old('last_name', $candidate ? $candidate->last_name : '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" 
                                value="{{ old('date_of_birth', ($candidate && $candidate->date_of_birth) ? $candidate->date_of_birth->format('Y-m-d') : '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" name="phone" 
                                value="{{ old('phone', $candidate ? $candidate->phone : '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                @error('phone') border-red-500 @enderror">
                            @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea id="address" name="address" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                @error('address') border-red-500 @enderror">{{ old('address', $candidate ? $candidate->address : '') }}</textarea>
                            @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="id_card" class="block text-sm font-medium text-gray-700">ID Card (JPEG, PNG, PDF only, max 5MB)</label>
                            <div class="mt-1">
                                <input type="file" id="id_card" name="id_card" {{ $candidate && $candidate->id_card_path ? '' : 'required' }}
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100
                                    @error('id_card') border-red-500 @enderror">
                            </div>
                            @error('id_card')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($candidate && $candidate->id_card_path)
                            <p class="mt-2 text-sm text-gray-500">
                                Current ID: <a href="{{ Storage::url($candidate->id_card_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View ID</a>
                            </p>
                            @endif
                        </div>

                        <div class="pt-5">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Submit Documents
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection