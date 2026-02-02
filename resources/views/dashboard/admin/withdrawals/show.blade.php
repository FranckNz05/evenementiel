@extends('layouts.dashboard')

@section('title', 'Détails du retrait')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --primary-light: #1a237e;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

.withdrawal-detail-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.page-title-section p {
    color: var(--gray-600);
    margin: 0;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
}

.btn-secondary {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.detail-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 1.5rem;
    border: none;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
}

.info-value-large {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--success);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #b45309;
}

.status-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-completed {
    background: #d1fae5;
    color: #15803d;
}

.status-rejected {
    background: #fee2e2;
    color: #b91c1c;
}

.details-section {
    background: var(--gray-50);
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.details-section h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 1rem 0;
}

.details-table {
    width: 100%;
    border-collapse: collapse;
}

.details-table tr {
    border-bottom: 1px solid var(--gray-200);
}

.details-table tr:last-child {
    border-bottom: none;
}

.details-table td {
    padding: 0.75rem 0;
    font-size: 0.875rem;
}

.details-table td:first-child {
    color: var(--gray-600);
    font-weight: 600;
    width: 40%;
}

.details-table td:last-child {
    color: var(--gray-900);
    font-family: monospace;
}

@media (max-width: 768px) {
    .withdrawal-detail-page {
        padding: 1rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="withdrawal-detail-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-money-bill-wave me-2"></i>
                Détails du retrait
            </h1>
            <p>Informations complètes sur la demande de retrait</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="detail-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-info-circle"></i>
                Informations du retrait
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Référence</span>
                    <span class="info-value">{{ $withdrawal->transaction_reference }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Montant</span>
                    <span class="info-value-large">{{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut</span>
                    <span class="status-badge status-{{ $withdrawal->status }}">
                        @if($withdrawal->status == 'pending')
                            <i class="fas fa-clock"></i> En attente
                        @elseif($withdrawal->status == 'processing')
                            <i class="fas fa-spinner fa-spin"></i> En traitement
                        @elseif($withdrawal->status == 'completed')
                            <i class="fas fa-check-circle"></i> Complété
                        @elseif($withdrawal->status == 'rejected')
                            <i class="fas fa-times-circle"></i> Rejeté
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Méthode de paiement</span>
                    <span class="info-value">
                        @if(stripos($withdrawal->payment_method, 'airtel') !== false)
                            <i class="fas fa-mobile-alt text-danger me-1"></i>
                        @elseif(stripos($withdrawal->payment_method, 'mtn') !== false)
                            <i class="fas fa-mobile-alt text-warning me-1"></i>
                        @endif
                        {{ $withdrawal->payment_method }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Numéro de téléphone</span>
                    <span class="info-value" style="font-family: monospace;">{{ $withdrawal->phone_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de demande</span>
                    <span class="info-value">{{ $withdrawal->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations organisateur -->
    <div class="detail-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-user"></i>
                Informations de l'organisateur
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nom complet</span>
                    <span class="info-value">{{ $withdrawal->organizer->prenom ?? 'N/A' }} {{ $withdrawal->organizer->nom ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $withdrawal->organizer->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Organisateur</span>
                    <span class="info-value" style="font-family: monospace;">#{{ $withdrawal->organizer_id }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails de traitement -->
    @if($withdrawal->processed_at || $withdrawal->processed_by || $withdrawal->rejection_reason)
    <div class="detail-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-cog"></i>
                Détails de traitement
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                @if($withdrawal->processed_at)
                <div class="info-item">
                    <span class="info-label">Date de traitement</span>
                    <span class="info-value">{{ $withdrawal->processed_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
                @if($withdrawal->processedBy)
                <div class="info-item">
                    <span class="info-label">Traité par</span>
                    <span class="info-value">{{ $withdrawal->processedBy->prenom ?? '' }} {{ $withdrawal->processedBy->nom ?? '' }}</span>
                </div>
                @endif
                @if($withdrawal->rejection_reason)
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Raison du rejet</span>
                    <span class="info-value" style="color: var(--danger);">{{ $withdrawal->rejection_reason }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Informations transaction -->
    @if($withdrawal->transaction_id || $withdrawal->airtel_money_id || $withdrawal->reference_id)
    <div class="detail-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-exchange-alt"></i>
                Informations de transaction
            </h3>
        </div>
        <div class="card-body">
            <div class="details-section">
                <table class="details-table">
                    @if($withdrawal->transaction_id)
                    <tr>
                        <td>ID Transaction</td>
                        <td>{{ $withdrawal->transaction_id }}</td>
                    </tr>
                    @endif
                    @if($withdrawal->airtel_money_id)
                    <tr>
                        <td>Airtel Money ID</td>
                        <td>{{ $withdrawal->airtel_money_id }}</td>
                    </tr>
                    @endif
                    @if($withdrawal->reference_id)
                    <tr>
                        <td>Référence ID</td>
                        <td>{{ $withdrawal->reference_id }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Détails techniques (JSON) -->
    @if($withdrawal->details)
    <div class="detail-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-code"></i>
                Détails techniques
            </h3>
        </div>
        <div class="card-body">
            <div class="details-section">
                <pre style="background: var(--gray-900); color: #10b981; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; font-size: 0.75rem; margin: 0;">{{ json_encode(json_decode($withdrawal->details), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($withdrawal->status == 'pending')
    <div class="detail-card">
        <div class="card-body">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-success" onclick="approveWithdrawal({{ $withdrawal->id }})">
                    <i class="fas fa-check"></i>
                    Approuver le retrait
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectWithdrawal({{ $withdrawal->id }})">
                    <i class="fas fa-times"></i>
                    Rejeter le retrait
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approuver le retrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir approuver ce retrait ?</p>
                    <div id="withdrawalDetails" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Code PIN Airtel Money</strong>
                            <small class="text-muted d-block">Requis pour effectuer le retrait</small>
                        </label>
                        <div class="d-flex justify-content-center gap-2 mb-2" id="pin-container">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin1"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin2"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin3"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin4"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                        </div>
                        <input type="hidden" id="pin" name="pin" required>
                        <div class="form-text text-center">4 chiffres</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approuver et traiter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rejeter le retrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir rejeter ce retrait ?</p>
                    <div id="rejectWithdrawalDetails" class="mb-3"></div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            <strong>Raison du rejet</strong> <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="rejection_reason" 
                                  name="rejection_reason" 
                                  rows="4" 
                                  required
                                  placeholder="Expliquez pourquoi ce retrait est rejeté..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion des champs PIN OTP (4 carrés)
document.addEventListener('DOMContentLoaded', function() {
    const pinInputs = document.querySelectorAll('.otp-input');
    const pinHidden = document.getElementById('pin');
    
    if (pinInputs.length === 4 && pinHidden) {
        function updatePinValue() {
            const pinValue = Array.from(pinInputs).map(input => input.value).join('');
            pinHidden.value = pinValue;
        }
        
        pinInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value.replace(/\D/g, '');
                e.target.value = value.substring(0, 1);
                updatePinValue();
                
                if (value && index < pinInputs.length - 1) {
                    pinInputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                    pinInputs[index - 1].value = '';
                    updatePinValue();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 4);
                
                for (let i = 0; i < pinInputs.length; i++) {
                    if (i < pastedData.length) {
                        pinInputs[i].value = pastedData[i];
                    } else {
                        pinInputs[i].value = '';
                    }
                }
                
                updatePinValue();
                const lastFilledIndex = Math.min(pastedData.length, pinInputs.length - 1);
                pinInputs[lastFilledIndex].focus();
            });
            
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        });
        
        const approveModal = document.getElementById('approveModal');
        if (approveModal) {
            approveModal.addEventListener('show.bs.modal', function() {
                pinInputs.forEach(input => {
                    input.value = '';
                });
                pinHidden.value = '';
                setTimeout(() => pinInputs[0].focus(), 300);
            });
        }
    }
});

function approveWithdrawal(withdrawalId) {
    document.getElementById('withdrawalDetails').innerHTML = `
        <div style="border: 1px solid var(--gray-200); border-radius: 0.5rem; padding: 1rem; background: var(--gray-50);">
            <strong>Référence:</strong> {{ $withdrawal->transaction_reference }}<br>
            <strong>Montant:</strong> {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA<br>
            <strong>Méthode:</strong> {{ $withdrawal->payment_method }}<br>
            <strong>Téléphone:</strong> {{ $withdrawal->phone_number }}
        </div>
    `;
    document.getElementById('approveForm').action = `{{ url('/Administrateur/withdrawals') }}/${withdrawalId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectWithdrawal(withdrawalId) {
    document.getElementById('rejectWithdrawalDetails').innerHTML = `
        <div style="border: 1px solid var(--gray-200); border-radius: 0.5rem; padding: 1rem; background: var(--gray-50);">
            <strong>Référence:</strong> {{ $withdrawal->transaction_reference }}<br>
            <strong>Montant:</strong> {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA<br>
            <strong>Méthode:</strong> {{ $withdrawal->payment_method }}<br>
            <strong>Téléphone:</strong> {{ $withdrawal->phone_number }}
        </div>
    `;
    document.getElementById('rejectForm').action = `{{ url('/Administrateur/withdrawals') }}/${withdrawalId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
@endsection

