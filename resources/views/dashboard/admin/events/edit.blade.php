@extends('layouts.dashboard')

@section('title', 'Modifier l\'événement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier l'événement</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Formulaire de modification
        </div>
        <div class="card-body">
            <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Titre de l'événement</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required maxlength="191">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') : '') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lieu" class="form-label">Lieu</label>
                        <input type="text" class="form-control @error('lieu') is-invalid @enderror" id="lieu" name="lieu" value="{{ old('lieu', $event->lieu) }}" required maxlength="100">
                        @error('lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse', $event->adresse) }}" required maxlength="191">
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" value="{{ old('ville', $event->ville) }}" required maxlength="255">
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="pays" class="form-label">Pays</label>
                        <input type="text" class="form-control @error('pays') is-invalid @enderror" id="pays" name="pays" value="{{ old('pays', $event->pays) }}" required maxlength="255">
                        @error('pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="adresse_map" class="form-label">Adresse pour la carte</label>
                        <input type="text" class="form-control @error('adresse_map') is-invalid @enderror" id="adresse_map" name="adresse_map" value="{{ old('adresse_map', $event->adresse_map) }}" maxlength="255">
                        @error('adresse_map')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="image" class="form-label">Image de l'événement</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($event->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $event->image) }}" alt="Image actuelle" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="event_type" class="form-label">Type d'événement</label>
                        <select class="form-select @error('event_type') is-invalid @enderror" id="event_type" name="event_type">
                            <option value="Espace libre" {{ old('event_type', $event->event_type) == 'Espace libre' ? 'selected' : '' }}>Espace libre</option>
                            <option value="Plan de salle" {{ old('event_type', $event->event_type) == 'Plan de salle' ? 'selected' : '' }}>Plan de salle</option>
                            <option value="Mixte" {{ old('event_type', $event->event_type) == 'Mixte' ? 'selected' : '' }}>Mixte</option>
                        </select>
                        @error('event_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="Payant" {{ old('status', $event->status) == 'Payant' ? 'selected' : '' }}>Payant</option>
                            <option value="Gratuit" {{ old('status', $event->status) == 'Gratuit' ? 'selected' : '' }}>Gratuit</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="etat" class="form-label">État</label>
                        <select class="form-select @error('etat') is-invalid @enderror" id="etat" name="etat">
                            <option value="En cours" {{ old('etat', $event->etat) == 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="Archivé" {{ old('etat', $event->etat) == 'Archivé' ? 'selected' : '' }}>Archivé</option>
                            <option value="Annulé" {{ old('etat', $event->etat) == 'Annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('etat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="publish_at" class="form-label">Date de publication</label>
                        <input type="datetime-local" class="form-control @error('publish_at') is-invalid @enderror" id="publish_at" name="publish_at" value="{{ old('publish_at', $event->publish_at ? \Carbon\Carbon::parse($event->publish_at)->format('Y-m-d\TH:i') : '') }}">
                        @error('publish_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Événement en vedette</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', $event->is_approved) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">Approuvé</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $event->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Publié</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="keywords" class="form-label">Mots-clés (séparés par des virgules)</label>
                        <input type="text" class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" value="{{ old('keywords', $event->keywords ? implode(', ', json_decode($event->keywords)) : '') }}">
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 