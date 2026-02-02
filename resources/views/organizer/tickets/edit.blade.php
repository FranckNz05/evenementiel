@extends('layouts.dashboard')

@section('title', 'Modifier un billet')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier un billet</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organizer.tickets.index') }}">Billets</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Formulaire de modification
        </div>
        <div class="card-body">
            <form action="{{ route('organizer.tickets.update', $ticket) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du billet</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $ticket->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix (FCFA)</label>
                            <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix', $ticket->prix) }}" min="0" step="1" required>
                            @error('prix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité totale</label>
                            <input type="number" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" value="{{ old('quantite', $ticket->quantite) }}" min="1" required>
                            @error('quantite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut">
                                <option value="actif" {{ (old('statut', $ticket->statut) == 'actif') ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ (old('statut', $ticket->statut) == 'inactif') ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Promotion (optionnel)</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="montant_promotionnel" class="form-label">Prix promotionnel (FCFA)</label>
                            <input type="number" class="form-control @error('montant_promotionnel') is-invalid @enderror" id="montant_promotionnel" name="montant_promotionnel" value="{{ old('montant_promotionnel', $ticket->montant_promotionnel) }}" min="0" step="1">
                            @error('montant_promotionnel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="promotion_start" class="form-label">Date de début</label>
                            <input type="datetime-local" class="form-control @error('promotion_start') is-invalid @enderror" id="promotion_start" name="promotion_start" value="{{ old('promotion_start', $ticket->promotion_start ? $ticket->promotion_start->format('Y-m-d\TH:i') : '') }}">
                            @error('promotion_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="promotion_end" class="form-label">Date de fin</label>
                            <input type="datetime-local" class="form-control @error('promotion_end') is-invalid @enderror" id="promotion_end" name="promotion_end" value="{{ old('promotion_end', $ticket->promotion_end ? $ticket->promotion_end->format('Y-m-d\TH:i') : '') }}">
                            @error('promotion_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('organizer.tickets.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
