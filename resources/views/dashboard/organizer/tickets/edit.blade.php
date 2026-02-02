@extends('layouts.dashboard')

@section('title', 'Modifier un billet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier un billet</h1>
        <a href="{{ route('organizer.tickets.index') }}" class="modern-btn btn-secondary-modern">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    <div class="modern-card">
        <div class="card-header-modern">
            <h6 class="m-0 font-weight-bold text-primary">{{ $ticket->nom }} - {{ $ticket->event->title }}</h6>
        </div>
        <div class="card-body-modern">
            <form action="{{ route('organizer.tickets.update', $ticket) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom">Nom du billet</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $ticket->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </