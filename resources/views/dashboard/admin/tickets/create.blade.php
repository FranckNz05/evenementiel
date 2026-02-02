@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Créer un billet</h1>
        <a href="{{ route('admin.tickets.index') }}" class="modern-btn btn-secondary-modern">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body-modern">
            <form action="{{ route('admin.tickets.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="event_id" class="form-label">Événement <span class="text-danger">*</span></label>
                        <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un événement</option>
                            @forelse($events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                            @empty
                                <option value="" disabled>Aucun événement disponible</option>
                            @endforelse
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Organisateur<span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un organisateur</option>
                            @forelse($users as $user)
                                <option value="{{ $user->id }}">{{ $user->prenom }} {{ $user->nom }} ({{ $user->email }})</option>
                            @empty
                                <option value="" disabled>Aucun utilisateur disponible</option>
                            @endforelse
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="ticket_type" class="form-label">Type de billet<span class="text-danger">*</span></label>
                        <input type="text" name="ticket_type" id="ticket_type" class="form-control @error('ticket_type') is-invalid @enderror" value="{{ old('ticket_type') }}" required>
                        @error('ticket_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="price" class="form-label">Prix <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required>
                            <span class="input-group-text">XAF</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="valid_until" class="form-label">Valide jusqu'au <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}" required>
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-2">Réinitialiser</button>
                    <button type="submit" class="modern-btn btn-primary-modern">
                        <i class="fas fa-save me-1"></i> Créer le billet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
