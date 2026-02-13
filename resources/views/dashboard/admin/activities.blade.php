@extends('layouts.dashboard')

@section('title', 'Toutes les activités')

@push('styles')
<style>
    /* Variables CSS personnalisées - Design MokiliEvent */
    :root {
        --bleu-nuit: #0f1a3d;
        --bleu-nuit-clair: #1a237e;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --white: #ffffff;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Tableau des activités */
    .activities-table {
        width: 100%;
        border-collapse: collapse;
    }

    .activities-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .activities-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-700) !important;
        border-bottom: 2px solid var(--gray-200);
    }

    .activities-table tbody tr {
        border-bottom: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .activities-table tbody tr:hover {
        background: var(--gray-50);
    }

    .activities-table tbody tr:last-child {
        border-bottom: none;
    }

    .activities-table td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--gray-800) !important;
    }

    .activity-icon-cell {
        width: 50px;
        text-align: center;
    }

    .activity-icon-table {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(15, 26, 61, 0.15);
    }

    .activity-icon-table i {
        color: #ffffff !important;
        font-size: 0.875rem;
    }

    .activity-description-cell {
        font-weight: 600;
        color: #000000 !important;
        font-size: 0.95rem;
    }

    .activity-user-cell {
        color: var(--gray-600) !important;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-user-cell i {
        color: var(--bleu-nuit) !important;
        font-size: 0.75rem;
    }

    .activity-time-cell {
        color: var(--gray-500) !important;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .activity-properties-cell {
        font-size: 0.8rem;
        color: var(--gray-600) !important;
    }

    .activity-properties-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: var(--gray-100);
        border-radius: 0.375rem;
        font-size: 0.75rem;
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .activity-properties-badge strong {
        color: var(--bleu-nuit) !important;
        font-weight: 600;
        margin-right: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .activities-table {
            font-size: 0.875rem;
        }
        
        .activities-table th,
        .activities-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .activities-table th:nth-child(4),
        .activities-table td:nth-child(4) {
            display: none;
        }
        
        .activities-table th:nth-child(3),
        .activities-table td:nth-child(3) {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>Toutes les activités</h5>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="activities-table">
                    <thead>
                        <tr>
                            <th class="activity-icon-cell"></th>
                            <th>Description</th>
                            <th>Utilisateur</th>
                            <th>Propriétés</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td class="activity-icon-cell">
                                    <div class="activity-icon-table">
                                        @if(str_contains(strtolower($activity['description'] ?? ''), 'créé') || str_contains(strtolower($activity['description'] ?? ''), 'création'))
                                            <i class="fas fa-plus-circle"></i>
                                        @elseif(str_contains(strtolower($activity['description'] ?? ''), 'modifié') || str_contains(strtolower($activity['description'] ?? ''), 'mise à jour'))
                                            <i class="fas fa-edit"></i>
                                        @elseif(str_contains(strtolower($activity['description'] ?? ''), 'supprimé') || str_contains(strtolower($activity['description'] ?? ''), 'suppression'))
                                            <i class="fas fa-trash-alt"></i>
                                        @elseif(str_contains(strtolower($activity['description'] ?? ''), 'paiement') || str_contains(strtolower($activity['description'] ?? ''), 'payé'))
                                            <i class="fas fa-money-bill-wave"></i>
                                        @elseif(str_contains(strtolower($activity['description'] ?? ''), 'retrait') || str_contains(strtolower($activity['description'] ?? ''), 'withdrawal'))
                                            <i class="fas fa-hand-holding-usd"></i>
                                        @else
                                            <i class="fas fa-circle"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="activity-description-cell">
                                    {{ $activity['description'] }}
                                </td>
                                <td>
                                    <div class="activity-user-cell">
                                        <i class="fas fa-user"></i>
                                        <span>{{ $activity['user_name'] }}</span>
                                    </div>
                                </td>
                                <td class="activity-properties-cell">
                                    @if(!empty($activity['properties']))
                                        @foreach($activity['properties'] as $key => $value)
                                            <span class="activity-properties-badge">
                                                <strong>{{ ucfirst($key) }}:</strong>
                                                {{ is_array($value) ? json_encode($value) : (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="activity-time-cell">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ isset($activity['created_at']) && $activity['created_at'] instanceof \Carbon\Carbon ? $activity['created_at']->diffForHumans() : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-inbox fa-3x" style="color: var(--gray-500) !important;"></i>
                                    </div>
                                    <p class="text-muted mb-0">Aucune activité</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="p-3 border-top">
                    {{ $activities->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

