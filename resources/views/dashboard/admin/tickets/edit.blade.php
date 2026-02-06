@extends('layouts.dashboard')

@section('title', 'Modifier le Ticket')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Modifier le Ticket</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tickets</a></li>
                    <li class="breadcrumb-item active">Édition</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-light shadow-sm border">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header-modern">
                    <h5 class="card-title my-1 text-white">
                        <i class="fas fa-ticket-alt me-2"></i>Édition : {{ $ticket->name }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-primary fa-2x me-3"></i>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Événement associé</small>
                                <span class="fw-bold text-dark">{{ $ticket->event->name }}</span>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark">Nom du ticket</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $ticket->name) }}" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="price" class="form-label fw-bold text-dark">Prix de vente</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $ticket->price) }}" min="0" required>
                                    <span class="input-group-text bg-light fw-bold border-start-0 text-dark">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity" class="form-label fw-bold text-dark">Stock total (Capacité)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $ticket->quantity) }}" min="0" required>
                                    <span class="input-group-text bg-light fw-bold border-start-0"><i class="fas fa-box"></i></span>
                                </div>
                                <div class="form-text small mt-1">
                                    <i class="fas fa-info-circle me-1"></i> Tickets déjà vendus : <strong>{{ $ticket->orders->sum('pivot.quantity') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark">Description / Avantages</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Ex: Accès VIP, boisson offerte...">{{ old('description', $ticket->description) }}</textarea>
                        </div>

                        <div class="text-end pt-3">
                            <hr class="mb-4">
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border px-4 me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary-modern shadow-sm px-5">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
