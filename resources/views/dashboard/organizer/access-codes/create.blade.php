@extends('layouts.dashboard')

@section('title', 'Créer un code d\'accès')

@section('content')
<div class="container-fluid">
    <div class="modern-card">
        <div class="card-header-modern">
            <h6 class="m-0 font-weight-bold text-primary">Créer un nouveau code d'accès</h6>
        </div>
        <div class="card-body-modern">
            <form action="{{ route('access-codes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="event_id" class="form-label">Événement</label>
                    <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                        <option value="">Sélectionnez un événement</option>
                        @forelse($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @empty
                            <option value="" disabled>Aucun événement disponible</option>
                        @endforelse
                    </select>
                    @error('event_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="valid_from" class="form-label">Valide à partir de</label>
                        <input type="datetime-local" class="form-control @error('valid_from') is-invalid @enderror" id="valid_from" name="valid_from" value="{{ old('valid_from', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="valid_until" class="form-label">Valide jusqu'à</label>
                        <input type="datetime-local" class="form-control @error('valid_until') is-invalid @enderror" id="valid_until" name="valid_until" value="{{ old('valid_until', now()->addDays(1)->format('Y-m-d\TH:i')) }}" required>
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optionnel)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('access-codes.index') }}" class="modern-btn btn-secondary-modern">Annuler</a>
                    <button type="submit" class="modern-btn btn-primary-modern">Créer le code d'accès</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection