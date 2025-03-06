@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Modifier un Groupe CME</h1>
            <h5 class="text-muted">{{ $group->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.cme.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informations du groupe</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cme.update', $group->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du groupe</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                value="{{ old('name', $group->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Examinateur</label>
                            <select class="form-select" id="staff_id" name="staff_id" required>
                                <option value="">Sélectionner un staff</option>
                                @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ old('staff_id', $group->staff_id) == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="session_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="session_date" name="session_date" 
                                value="{{ old('session_date', $group->session_date->format('Y-m-d')) }}" required>
                            @error('session_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="session_time" class="form-label">Session</label>
                            <select class="form-select" id="session_time" name="session_time" required>
                                <option value="morning" {{ old('session_time', $group->session_time) == 'morning' ? 'selected' : '' }}>Matin (9h-12h)</option>
                                <option value="afternoon" {{ old('session_time', $group->session_time) == 'afternoon' ? 'selected' : '' }}>Après-midi (14h-17h)</option>
                            </select>
                            @error('session_time')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Lieu</label>
                    <input type="text" class="form-control" id="location" name="location" 
                        value="{{ old('location', $group->interviews->first() ? $group->interviews->first()->location : 'YouCode Youssoufia') }}" required>
                    @error('location')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Candidats (4 maximum)</label>
                    
                    @php
                        // Get currently assigned candidate IDs
                        $assignedCandidateIds = $group->interviews->pluck('candidate_id')->toArray();
                    @endphp
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">Sélection</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableCandidates as $candidate)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input candidate-check" type="checkbox" 
                                                name="candidates[]" value="{{ $candidate->id }}" 
                                                id="candidate_{{ $candidate->id }}"
                                                {{ in_array($candidate->id, old('candidates', $assignedCandidateIds)) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $candidate->name }}</td>
                                    <td>{{ $candidate->email }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @error('candidates')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour le groupe CME
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.candidate-check');
    const maxAllowed = 4;
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.candidate-check:checked').length;
            
            if (checkedCount > maxAllowed) {
                this.checked = false;
                alert(`Vous pouvez sélectionner au maximum ${maxAllowed} candidats pour un groupe CME.`);
            }
        });
    });
});
</script>
@endsection