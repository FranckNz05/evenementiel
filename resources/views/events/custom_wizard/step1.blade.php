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
        background: linear-gradient(90deg, #0f3460 0%, #1a5490 100%);
        height: 100%;
        width: 33.33%;
        border-radius: 10px;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(15, 52, 96, 0.3);
    }
    
    .wizard-body {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .offer-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 15px 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .offer-badge-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .offer-badge-icon {
        font-size: 32px;
        background: rgba(255, 255, 255, 0.2);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .offer-badge-text h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }
    
    .offer-badge-text p {
        margin: 5px 0 0 0;
        font-size: 14px;
        opacity: 0.9;
    }
    
    .offer-badge-price {
        font-size: 24px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 10px;
    }
    
    .form-group-enhanced {
        margin-bottom: 30px;
    }
    
    .form-label-enhanced {
        font-weight: 600;
        color: #0f3460;
        margin-bottom: 10px;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-label-enhanced i {
        color: #5dade2;
        font-size: 18px;
    }
    
    .form-control-enhanced {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #ffffff;
        color: #1a5490;
    }
    
    .form-control-enhanced:focus {
        border-color: #5dade2;
        box-shadow: 0 0 0 3px rgba(93, 173, 226, 0.1);
        outline: none;
    }
    
    .form-control-enhanced::placeholder {
        color: #a0aec0;
    }
    
    .form-select-enhanced {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #ffffff;
        color: #1a5490;
        cursor: pointer;
    }
    
    .form-select-enhanced:focus {
        border-color: #5dade2;
        box-shadow: 0 0 0 3px rgba(93, 173, 226, 0.1);
        outline: none;
    }
    
    .form-select-enhanced option {
        padding: 10px;
        color: #1a5490;
    }
    
    .wizard-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
    }
    
    .btn-wizard-primary {
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
    }
    
    .btn-wizard-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 52, 96, 0.4);
    }
    
    .btn-wizard-primary:active {
        transform: translateY(0);
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
    
    .step-indicator.active {
        background: linear-gradient(135deg, #0f3460 0%, #1a5490 100%);
        color: #ffffff;
        border-color: #0f3460;
        box-shadow: 0 5px 15px rgba(15, 52, 96, 0.3);
    }
    
    .step-indicator.inactive {
        background: #ffffff;
        color: #a0aec0;
        border-color: #e2e8f0;
    }
    
    .step-indicator.completed {
        background: #28a745;
        color: #ffffff;
        border-color: #28a745;
    }
    
    .help-text {
        color: #718096;
        font-size: 14px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .help-text i {
        color: #5dade2;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        background: #fee;
        padding: 8px 12px;
        border-radius: 8px;
        border-left: 3px solid #dc3545;
    }
    
    .invalid-feedback::before {
        content: '⚠';
        font-size: 16px;
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
        
        .offer-badge {
            flex-direction: column;
            text-align: center;
        }
        
        .wizard-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-wizard-primary {
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
            <h1><i class="fas fa-magic me-2"></i>Création d'événement personnalisé</h1>
            <p>Étape 1 sur 3 • Informations de base</p>
            <div class="wizard-progress">
                <div class="wizard-progress-bar"></div>
            </div>
        </div>
        
        <!-- Indicateur des étapes -->
        <div class="wizard-steps-indicator">
            <div class="step-indicator active">1</div>
            <div class="step-indicator inactive">2</div>
            <div class="step-indicator inactive">3</div>
        </div>
        
        <!-- Corps du formulaire -->
        <div class="wizard-body">
                        @if($offer ?? null)
                        <!-- Badge de l'offre -->
                        <div class="offer-badge">
                            <div class="offer-badge-content">
                                <div class="offer-badge-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="offer-badge-text">
                                    <h4>Plan {{ ucfirst($offer['label'] ?? 'Premium') }}</h4>
                                    <p>Vous créez un événement avec votre plan acheté</p>
                                </div>
                            </div>
                            @if(isset($offer['price']))
                            <div class="offer-badge-price">
                                {{ number_format($offer['price'], 0, ',', ' ') }} FCFA
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <form action="{{ route('custom-events.wizard.step1.store') }}" method="POST" id="wizard-form">
                            @csrf
                            
                            <!-- Titre de l'événement -->
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="fas fa-heading"></i>
                                    Titre de l'événement *
                                </label>
                                <input 
                                    type="text" 
                                    name="title" 
                                    class="form-control form-control-enhanced @error('title') is-invalid @enderror" 
                                    value="{{ old('title') }}" 
                                    placeholder="Ex: Mariage de Jean et Marie"
                                    required
                                    autofocus
                                >
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    Choisissez un titre accrocheur pour votre événement
                                </div>
                            </div>
                            
                            <!-- Type d'événement -->
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="fas fa-tags"></i>
                                    Type d'événement *
                                </label>
                                <select 
                                    name="type" 
                                    class="form-select form-select-enhanced @error('type') is-invalid @enderror" 
                                    required
                                >
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Sélectionnez un type...</option>
                                    <option value="mariage" {{ old('type') === 'mariage' ? 'selected' : '' }}>
                                        💒 Mariage
                                    </option>
                                    <option value="anniversaire" {{ old('type') === 'anniversaire' ? 'selected' : '' }}>
                                        🎂 Anniversaire
                                    </option>
                                    <option value="conference" {{ old('type') === 'conference' ? 'selected' : '' }}>
                                        🎤 Conférence
                                    </option>
                                    <option value="soiree" {{ old('type') === 'soiree' ? 'selected' : '' }}>
                                        🎉 Soirée
                                    </option>
                                    <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>
                                        🎊 Autre
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    Sélectionnez le type d'événement le plus approprié
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="wizard-actions">
                                <div></div>
                                <button type="submit" class="btn-wizard-primary">
                                    Continuer
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Information supplémentaire -->
                        <div class="info-box-custom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="info-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div>
                                    <h5>
                                        <i class="fas fa-info-circle me-2"></i>Conseil
                                    </h5>
                                    <p>
                                        Prenez le temps de choisir un titre accrocheur et un type approprié. Ces informations seront utilisées pour personnaliser votre événement et aider vos invités à mieux comprendre le type d'événement.
                                    </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('wizard-form');
        const inputs = form.querySelectorAll('input, select');
        
        // Ajouter des animations aux champs
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Validation en temps réel
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires');
            }
        });
    });
</script>
@endsection
