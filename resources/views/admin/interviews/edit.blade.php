@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Modifier un Entretien</h1>
            <h5 class="text-muted">Candidat: {{ $interview->candidate->name }}</h5>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.interviews.update', $interview->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type d'entretien</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="technical" {{ $interview->type == 'technical' ? 'selected' : '' }}>Technique</option>
                                <option value="administrative" {{ $interview->type == 'administrative' ? 'selected' : '' }}>Administratif</option>
                                <option value="CME" {{ $interview->type == 'CME' ? 'selected' : '' }}>CME</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $interview->location) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Examinateur</label>
                            <select class="form-select" id="staff_id" name="staff_id" required>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ $interview->staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="scheduled" {{ $interview->status == 'scheduled' ? 'selected' : '' }}>Planifié</option>
                                <option value="completed" {{ $interview->status == 'completed' ? 'selected' : '' }}>Terminé</option>
                                <option value="cancelled" {{ $interview->status == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $interview->date->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Heure de début</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($interview->start_time)->format('H:i')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Heure de fin</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($interview->end_time)->format('H:i')) }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optionnel)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $interview->notes) }}</textarea>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour l'entretien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection