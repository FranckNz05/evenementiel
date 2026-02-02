@extends('layouts.dashboard')

@section('title', 'Toutes les activités')

@push('styles')
<style>
    /* Section des activités récentes - cartes réduites */
    .activity-item {
        border-left: 3px solid var(--bleu-nuit, #0f1a3d);
        background: #ffffff;
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .activity-description {
        font-weight: 600;
        color: #000000 !important;
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
    }

    .activity-user {
        color: #6b7280 !important;
        font-size: 0.85rem;
        margin-bottom: 0.2rem;
    }

    .activity-meta {
        font-size: 0.75rem;
        color: #6b7280 !important;
    }

</style>
@endpush

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>Toutes les activités</h5>
        </div>
        <div class="card-body">
            @forelse($activities as $activity)
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="activity-description">{{ $activity['description'] }}</div>
                            <div class="activity-user">Par {{ $activity['user_name'] }}</div>
                            @if(!empty($activity['properties']))
                                <div class="activity-meta">
                                    @foreach($activity['properties'] as $key => $value)
                                        <span class="me-3" style="color: #6b7280;">
                                            <strong style="color: #000000;">{{ ucfirst($key) }}:</strong> 
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">{{ isset($activity['created_at']) && $activity['created_at'] instanceof \Carbon\Carbon ? $activity['created_at']->diffForHumans() : '' }}</small>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune activité</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="mt-4">
                    {{ $activities->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

