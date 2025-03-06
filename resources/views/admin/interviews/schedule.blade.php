@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Calendrier des Entretiens</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Examinateurs</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.interviews.schedule') }}" class="list-group-item list-group-item-action {{ !$staffId ? 'active' : '' }}">
                            Tous les examinateurs
                        </a>
                        @foreach($staffMembers as $staff)
                            <a href="{{ route('admin.interviews.schedule', $staff->id) }}" 
                               class="list-group-item list-group-item-action {{ $staffId == $staff->id ? 'active' : '' }}">
                                {{ $staff->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        @if($staffId)
                            Entretiens prévus pour {{ $staffMembers->find($staffId)->name }}
                        @else
                            Tous les entretiens à venir
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($interviews) && $interviews->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Horaire</th>
                                        <th>Candidat</th>
                                        <th>Type</th>
                                        <th>Lieu</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentDate = null;
                                    @endphp
                                    @foreach($interviews as $interview)
                                        @php
                                            $interviewDate = \Carbon\Carbon::parse($interview->date)->format('Y-m-d');
                                        @endphp
                                        
                                        @if($currentDate != $interviewDate)
                                            @php
                                                $currentDate = $interviewDate;
                                            @endphp
                                            <tr class="table-secondary">
                                                <th colspan="6">
                                                    {{ \Carbon\Carbon::parse($interview->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                                </th>
                                            </tr>
                                        @endif
                                        
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($interview->date)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($interview->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($interview->end_time)->format('H:i') }}</td>
                                            <td>{{ $interview->candidate->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $interview->type == 'technical' ? 'primary' : ($interview->type == 'administrative' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($interview->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $interview->location }}</td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.interviews.show', $interview->id) }}" class="btn btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            @if($staffId)
                                <p class="mb-0">Aucun entretien planifié pour cet examinateur.</p>
                            @else
                                <p class="mb-0">Aucun entretien planifié.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection