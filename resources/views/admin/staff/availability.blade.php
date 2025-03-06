@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Disponibilité de l'Examinateur</h1>
            <h5 class="text-muted">{{ $staff->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Définir les disponibilités hebdomadaires</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.staff.availability.update', $staff->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jour</th>
                                <th>Disponible</th>
                                <th>Heure de début</th>
                                <th>Heure de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                @php
                                    $dayAvailability = $availabilities->where('day_of_week', $day)->first();
                                @endphp
                                <tr>
                                    <td>
                                        {{ ucfirst(__('app.' . $day)) }}
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input day-available" type="checkbox" 
                                                id="{{ $day }}_available" 
                                                name="availabilities[{{ $day }}][is_available]" 
                                                value="1" 
                                                {{ $dayAvailability && $dayAvailability->is_available ? 'checked' : '' }}
                                                data-day="{{ $day }}">
                                            <label class="form-check-label" for="{{ $day }}_available">
                                                Disponible
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control time-input" 
                                            id="{{ $day }}_start" 
                                            name="availabilities[{{ $day }}][start_time]" 
                                            value="{{ $dayAvailability ? \Carbon\Carbon::parse($dayAvailability->start_time)->format('H:i') : '09:00' }}"
                                            {{ $dayAvailability && !$dayAvailability->is_available ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control time-input" 
                                            id="{{ $day }}_end" 
                                            name="availabilities[{{ $day }}][end_time]" 
                                            value="{{ $dayAvailability ? \Carbon\Carbon::parse($dayAvailability->end_time)->format('H:i') : '17:00' }}"
                                            {{ $dayAvailability && !$dayAvailability->is_available ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les disponibilités
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Entretiens programmés</h5>
        </div>
        <div class="card-body">
            @if($interviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Candidat</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interviews as $interview)
                                <tr>
                                    <td>{{ $interview->candidate->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($interview->scheduled_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($interview->scheduled_time)->format('H:i') }}</td>
                                    <td>{{ $interview->location }}</td>
                                    <td>
                                        <span class="badge bg-{{ $interview->status_color }}">
                                            {{ $interview->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Aucun entretien programmé.</p>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const availabilityCheckboxes = document.querySelectorAll('.day-available');
    
    availabilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const startInput = document.getElementById(`${day}_start`);
            const endInput = document.getElementById(`${day}_end`);
            
            if (this.checked) {
                startInput.disabled = false;
                endInput.disabled = false;
            } else {
                startInput.disabled = true;
                endInput.disabled = true;
            }
        });
    });
});
</script>
@endsection