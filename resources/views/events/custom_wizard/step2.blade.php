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
        width: 66.66%;
        border-radius: 10px;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(15, 52, 96, 0.3);
    }
    
    .wizard-body {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
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
    
    textarea.form-control-enhanced {
        min-height: 120px;
        resize: vertical;
    }
    
    .wizard-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
    }
    
    .btn-wizard-secondary {
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
    
    .btn-wizard-secondary:hover {
        background: #f7fafc;
        border-color: #0f3460;
        transform: translateY(-2px);
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
    
    .row-enhanced {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .col-enhanced {
        flex: 1;
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
        
        .wizard-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-wizard-primary,
        .btn-wizard-secondary {
            width: 100%;
            justify-content: center;
        }
        
        .step-indicator {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
        
        .row-enhanced {
            flex-direction: column;
        }
    }
</style>

<div class="wizard-container">
    <div class="container">
        <!-- En-tête avec barre de progression -->
        <div class="wizard-header">
            <h1><i class="fas fa-calendar-alt me-2"></i>Création d'événement personnalisé</h1>
            <p>Étape 2 sur 3 • Dates et lieu</p>
            <div class="wizard-progress">
                <div class="wizard-progress-bar"></div>
            </div>
        </div>
        
        <!-- Indicateur des étapes -->
        <div class="wizard-steps-indicator">
            <div class="step-indicator completed">1</div>
            <div class="step-indicator active">2</div>
            <div class="step-indicator inactive">3</div>
        </div>
        
        <!-- Corps du formulaire -->
        <div class="wizard-body">
            <form action="{{ route('custom-events.wizard.step2.store') }}" method="POST" id="wizard-form">
                @csrf
                
                <!-- Dates -->
                <div class="row-enhanced">
                    <div class="col-enhanced">
                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-clock"></i>
                                Date de début *
                            </label>
                            <input 
                                type="datetime-local" 
                                name="start_date" 
                                class="form-control form-control-enhanced @error('start_date') is-invalid @enderror" 
                                value="{{ old('start_date', now()->addDays(7)->format('Y-m-d\TH:i')) }}" 
                                required
                            >
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                <i class="fas fa-info-circle"></i>
                                Sélectionnez la date et l'heure de début de votre événement
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-enhanced">
                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-stopwatch"></i>
                                Date de fin
                            </label>
                            <input 
                                type="datetime-local" 
                                name="end_date" 
                                class="form-control form-control-enhanced @error('end_date') is-invalid @enderror" 
                                value="{{ old('end_date') }}"
                            >
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                <i class="fas fa-info-circle"></i>
                                Optionnel : Date et heure de fin de l'événement
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lieu -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="fas fa-map-marker-alt"></i>
                        Lieu de l'événement *
                    </label>
                    <input 
                        type="text" 
                        name="location" 
                        class="form-control form-control-enhanced @error('location') is-invalid @enderror" 
                        value="{{ old('location') }}" 
                        placeholder="Ex: Centre de conférence, Hôtel XYZ, Salle des fêtes..."
                        required
                    >
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Indiquez le lieu où se déroulera l'événement
                    </div>
                </div>
                
                <!-- Description -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="fas fa-align-left"></i>
                        Description de l'événement
                    </label>
                    <textarea 
                        name="description" 
                        class="form-control form-control-enhanced @error('description') is-invalid @enderror" 
                        rows="5"
                        placeholder="Décrivez votre événement, son programme, ses objectifs..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Optionnel : Ajoutez une description détaillée de votre événement
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="wizard-actions">
                    <a href="{{ route('custom-events.wizard.step1') }}" class="btn-wizard-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
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
                            Assurez-vous de bien indiquer le lieu exact de l'événement et les dates précises. Ces informations seront importantes pour vos invités qui planifieront leur venue.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('wizard-form');
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
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
        
        // Validation de la date de fin
        const startDateInput = form.querySelector('input[name="start_date"]');
        const endDateInput = form.querySelector('input[name="end_date"]');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && new Date(endDateInput.value) <= new Date(this.value)) {
                    endDateInput.setCustomValidity('La date de fin doit être après la date de début');
                } else {
                    endDateInput.setCustomValidity('');
                }
            });
            
            endDateInput.addEventListener('change', function() {
                if (this.value && new Date(this.value) <= new Date(startDateInput.value)) {
                    this.setCustomValidity('La date de fin doit être après la date de début');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
</script>
@endsection
