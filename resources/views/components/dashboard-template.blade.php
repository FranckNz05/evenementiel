@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête de page --}}
    @if(isset($pageTitle))
        <x-page-header 
            :title="$pageTitle" 
            :icon="$pageIcon ?? 'fas fa-dashboard'"
            :subtitle="$pageSubtitle ?? null">
            <x-slot:actions>
                {{ $headerActions ?? '' }}
            </x-slot:actions>
        </x-page-header>
    @endif

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="modern-alert alert-success-modern fade-in">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="modern-alert alert-danger-modern fade-in">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="modern-alert alert-warning-modern fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="modern-alert alert-info-modern fade-in">
            <i class="fas fa-info-circle"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    {{-- Cartes de statistiques (optionnel) --}}
    @if(isset($stats) && count($stats) > 0)
        <div class="stats-grid">
            @foreach($stats as $stat)
                <x-stat-card 
                    :number="$stat['number']" 
                    :label="$stat['label']" 
                    :icon="$stat['icon']" 
                />
            @endforeach
        </div>
    @endif

    {{-- Contenu principal --}}
    {{ $slot }}
</div>
@endsection

