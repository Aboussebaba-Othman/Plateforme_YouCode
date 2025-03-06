@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Planifier un Entretien</h1>
            <h5 class="text-muted">Candidat: {{ $candidate->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.candidate.view', $candidate->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour au profil
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.interviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

                <!-- Rest of your form fields -->


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="auto_schedule" id="auto_schedule"
                                value="1" {{ old('auto_schedule') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_schedule">
                                <strong>Planification automatique</strong> (date et examinateur selon disponibilités)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type d'entretien</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>Technique
                                </option>
                                <option value="administrative" {{ old('type') == 'administrative' ? 'selected' : '' }}>
                                    Administratif</option>
                                <option value="CME" {{ old('type') == 'CME' ? 'selected' : '' }}>CME</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu</label>
                            <input type="text" class="form-control" id="location" name="location"
                                value="{{ old('location', 'YouCode Youssoufia') }}" required>
                        </div>
                    </div>
                </div>

                <div id="manual-scheduling" class="{{ old('auto_schedule') ? 'd-none' : '' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Examinateur</label>
                                <select class="form-select" id="staff_id" name="staff_id">
                                    <option value="">Sélectionner un examinateur</option>
                                    @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}"
                                        {{ old('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Heure de début</label>
                                <input type="time" class="form-control" id="start_time" name="start_time"
                                    value="{{ old('start_time', '09:00') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Heure de fin</label>
                                <input type="time" class="form-control" id="end_time" name="end_time"
                                    value="{{ old('end_time', '10:00') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optionnel)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Planifier l'entretien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const autoScheduleCheckbox = document.getElementById('auto_schedule');
    const manualSchedulingDiv = document.getElementById('manual-scheduling');
    const staffSelect = document.getElementById('staff_id');
    const dateInput = document.getElementById('date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    autoScheduleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            manualSchedulingDiv.classList.add('d-none');
            staffSelect.removeAttribute('required');
            dateInput.removeAttribute('required');
            startTimeInput.removeAttribute('required');
            endTimeInput.removeAttribute('required');
        } else {
            manualSchedulingDiv.classList.remove('d-none');
            staffSelect.setAttribute('required', '');
            dateInput.setAttribute('required', '');
            startTimeInput.setAttribute('required', '');
            endTimeInput.setAttribute('required', '');
        }
    });
});
</script>
@endsection