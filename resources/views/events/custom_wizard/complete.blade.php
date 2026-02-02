@extends('layouts.dashboard')

@section('content')
<style>
    .wizard-container {
        background: #ffffff;
        min-height: 100vh;
        padding: 60px 0;
    }
    
    .wizard-header {
        color: #1a5490;
        padding: 40px 0;
        text-align: center;
        position: relative;
        margin-bottom: 40px;
    }
    
    .wizard-header h1 {
        color: #0f3460;
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .wizard-header p {
        color: #718096;
        font-size: 18px;
        margin: 0;
    }
    
    .wizard-progress {
        background: #e2e8f0;
        height: 8px;
        border-radius: 10px;
        margin-top: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        overflow: hidden;
    }
    
    .wizard-progress-bar {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        height: 100%;
        width: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    }
    
    .wizard-body {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .success-icon {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        margin: 0 auto 30px;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .success-message {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .success-message h2 {
        color: #0f3460;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .success-message p {
        color: #718096;
        font-size: 18px;
        margin: 0;
    }
    
    .event-summary-card {
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .event-summary-card h3 {
        color: #0f3460;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .event-summary-card h3 i {
        color: #5dade2;
    }
    
    .summary-row {
        display: flex;
        gap: 30px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .summary-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .summary-item {
        flex: 1;
    }
    
    .summary-label {
        color: #718096;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .summary-value {
        color: #0f3460;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .summary-value i {
        color: #5dade2;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 40px;
        flex-wrap: wrap;
    }
    
    .btn-action-primary {
        background: linear-gradient(135deg, #0f3460 0%, #1a5490 100%);
        color: #ffffff;
        padding: 15px 40px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 15px rgba(15, 52, 96, 0.3);
        text-decoration: none;
    }
    
    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 52, 96, 0.4);
        color: #ffffff;
    }
    
    .btn-action-secondary {
        background: #ffffff;
        color: #0f3460;
        padding: 15px 40px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    
    .btn-action-secondary:hover {
        background: #f7fafc;
        border-color: #0f3460;
        transform: translateY(-2px);
        color: #0f3460;
    }
    
    .wizard-steps-indicator {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
    }
    
    .step-indicator {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        transition: all 0.3s ease;
        border: 3px solid #e2e8f0;
    }
    
    .step-indicator.completed {
        background: #28a745;
        color: #ffffff;
        border-color: #28a745;
    }
    
    .info-box-custom {
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 25px;
        margin-top: 40px;
    }
    
    .info-box-custom h5 {
        color: #0f3460;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .info-box-custom p {
        color: #4a5568;
        margin: 0;
        line-height: 1.6;
    }
    
    .info-box-custom ul {
        color: #4a5568;
        margin: 10px 0 0 0;
        padding-left: 20px;
    }
    
    .info-box-custom li {
        margin-bottom: 8px;
    }
    
    .info-box-custom .info-icon {
        background: linear-gradient(135deg, #5dade2 0%, #1a5490 100%);
        color: #ffffff;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    
    .event-image-preview {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 12px;
        margin-top: 15px;
        border: 2px solid #e2e8f0;
    }
    
    @media (max-width: 768px) {
        .wizard-container {
            padding: 30px 0;
        }
        
        .wizard-header h1 {
            font-size: 28px;
        }
        
        .wizard-header p {
            font-size: 16px;
        }
        
        .wizard-body {
            padding: 0 15px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            font-size: 40px;
        }
        
        .success-message h2 {
            font-size: 24px;
        }
        
        .summary-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-action-primary,
        .btn-action-secondary {
            width: 100%;
            justify-content: center;
        }
        
        .step-indicator {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
    }
</style>

<div class="wizard-container">
    <div class="container">
        <!-- En-tête avec barre de progression -->
        <div class="wizard-header">
            <h1><i class="fas fa-check-circle me-2"></i>Création d'événement personnalisé</h1>
            <p>Complété avec succès !</p>
            <div class="wizard-progress">
                <div class="wizard-progress-bar"></div>
            </div>
        </div>
        
        <!-- Indicateur des étapes -->
        <div class="wizard-steps-indicator">
            <div class="step-indicator completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-indicator completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-indicator completed">
                <i class="fas fa-check"></i>
            </div>
        </div>
        
        <!-- Corps de la page -->
        <div class="wizard-body">
            <!-- Message de succès -->
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Félicitations !</h2>
                <p>Votre événement personnalisé a été créé avec succès.</p>
            </div>
            
            <!-- Résumé de l'événement -->
            <div class="event-summary-card">
                <h3>
                    <i class="fas fa-calendar-check"></i>
                    Résumé de votre événement
                </h3>
                
                <div class="summary-row">
                    <div class="summary-item">
                        <div class="summary-label">Titre</div>
                        <div class="summary-value">
                            <i class="fas fa-heading"></i>
                            {{ $event->title }}
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Type</div>
                        <div class="summary-value">
                            <i class="fas fa-tag"></i>
                            {{ $event->type ? ucfirst($event->type) : 'Non défini' }}
                        </div>
                    </div>
                </div>
                
                <div class="summary-row">
                    <div class="summary-item">
                        <div class="summary-label">Date de début</div>
                        <div class="summary-value">
                            <i class="fas fa-clock"></i>
                            {{ $event->start_date->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    @if($event->end_date)
                    <div class="summary-item">
                        <div class="summary-label">Date de fin</div>
                        <div class="summary-value">
                            <i class="fas fa-stopwatch"></i>
                            {{ $event->end_date->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="summary-row">
                    <div class="summary-item">
                        <div class="summary-label">Lieu</div>
                        <div class="summary-value">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $event->location }}
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Invités maximum</div>
                        <div class="summary-value">
                            <i class="fas fa-users"></i>
                            {{ $event->guest_limit ?? 'Illimité' }}
                        </div>
                    </div>
                </div>
                
                @if($event->description)
                <div class="summary-row">
                    <div class="summary-item" style="flex: 1;">
                        <div class="summary-label">Description</div>
                        <div class="summary-value" style="flex-direction: column; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-align-left"></i>
                            <p style="color: #4a5568; font-weight: normal; margin: 0;">{{ $event->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($event->image)
                <div class="summary-row">
                    <div class="summary-item" style="flex: 1;">
                        <div class="summary-label">Image</div>
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->image) }}" alt="{{ $event->title }}" class="event-image-preview">
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="action-buttons">
                <a href="{{ route('custom-events.show', $event) }}" class="btn-action-primary">
                    <i class="fas fa-eye"></i>
                    Voir mon événement
                </a>
                <a href="{{ route('custom-events.index') }}" class="btn-action-secondary">
                    <i class="fas fa-list"></i>
                    Mes événements
                </a>
            </div>
            
            <!-- Information supplémentaire -->
            <div class="info-box-custom">
                <div class="d-flex align-items-start gap-3">
                    <div class="info-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h5>
                            <i class="fas fa-info-circle me-2"></i>Prochaines étapes
                        </h5>
                        <p>
                            Votre événement est maintenant créé ! Voici ce que vous pouvez faire :
                        </p>
                        <ul>
                            <li><strong>Inviter des invités :</strong> Ajoutez des invités à votre événement depuis la page de gestion</li>
                            <li><strong>Partager l'événement :</strong> Partagez le lien d'invitation avec vos invités</li>
                            <li><strong>Gérer les invitations :</strong> Suivez les réponses et la présence de vos invités</li>
                            <li><strong>Modifier l'événement :</strong> Vous pouvez modifier les détails de l'événement à tout moment</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation de confetti ou effet de succès
        const successIcon = document.querySelector('.success-icon');
        if (successIcon) {
            successIcon.addEventListener('animationend', function() {
                this.style.animation = 'none';
            });
        }
    });
</script>
@endsection

