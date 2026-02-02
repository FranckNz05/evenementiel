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
        width: 100%;
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
    
    .form-file-enhanced {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        background: #f7fafc;
        cursor: pointer;
    }
    
    .form-file-enhanced:hover {
        border-color: #5dade2;
        background: #f0f7ff;
    }
    
    .form-file-enhanced input[type="file"] {
        display: none;
    }
    
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        cursor: pointer;
    }
    
    .file-upload-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5dade2 0%, #1a5490 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    
    .file-upload-text {
        color: #0f3460;
        font-weight: 600;
        font-size: 16px;
    }
    
    .file-upload-hint {
        color: #718096;
        font-size: 14px;
    }
    
    .file-preview {
        margin-top: 15px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
    }
    
    .file-preview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 10px;
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
            <h1><i class="fas fa-check-circle me-2"></i>Création d'événement personnalisé</h1>
            <p>Étape 3 sur 3 • Invités & image</p>
            <div class="wizard-progress">
                <div class="wizard-progress-bar"></div>
            </div>
        </div>
        
        <!-- Indicateur des étapes -->
        <div class="wizard-steps-indicator">
            <div class="step-indicator completed">1</div>
            <div class="step-indicator completed">2</div>
            <div class="step-indicator active">3</div>
        </div>
        
        <!-- Corps du formulaire -->
        <div class="wizard-body">
            <form action="{{ route('custom-events.wizard.step3.store') }}" method="POST" id="wizard-form" enctype="multipart/form-data">
                @csrf
                
                <!-- Nombre maximum d'invités -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="fas fa-users"></i>
                        Nombre maximum d'invités
                    </label>
                    <input 
                        type="number" 
                        name="guest_limit" 
                        class="form-control form-control-enhanced @error('guest_limit') is-invalid @enderror" 
                        value="{{ old('guest_limit') }}" 
                        min="1"
                        placeholder="Ex: 100"
                    >
                    @error('guest_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($offer['plan']))
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            Votre formule ({{ ucfirst($offer['plan'] ?? '') }}) limite le nombre d'invités. La valeur sera ajustée si nécessaire.
                        </div>
                    @else
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            Définissez le nombre maximum d'invités pour votre événement (optionnel)
                        </div>
                    @endif
                </div>
                
                <!-- Image de l'événement -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="fas fa-image"></i>
                        Image de l'événement
                    </label>
                    <div class="form-file-enhanced">
                        <label class="file-upload-label" for="image-upload">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div>
                                <div class="file-upload-text">Cliquez pour télécharger une image</div>
                                <div class="file-upload-hint">ou glissez-déposez l'image ici</div>
                            </div>
                        </label>
                        <input 
                            type="file" 
                            name="image" 
                            id="image-upload" 
                            class="form-control @error('image') is-invalid @enderror" 
                            accept="image/*"
                            onchange="previewImage(this)"
                        >
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="image-preview" class="file-preview" style="display: none;">
                        <img id="preview-img" src="" alt="Aperçu de l'image">
                    </div>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Téléchargez une image représentative de votre événement (JPG, PNG, format recommandé: 1920x1080px)
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="wizard-actions">
                    <a href="{{ route('custom-events.wizard.step2') }}" class="btn-wizard-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                    <button type="submit" class="btn-wizard-primary">
                        <i class="fas fa-check"></i>
                        Terminer
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
                            Une belle image rendra votre événement plus attractif pour vos invités. Assurez-vous que l'image soit de bonne qualité et représentative de votre événement. Le nombre d'invités peut être ajusté plus tard si nécessaire.
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
        const fileInput = document.getElementById('image-upload');
        const fileUploadLabel = document.querySelector('.file-upload-label');
        
        // Prévisualisation de l'image
        window.previewImage = function(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewDiv = document.getElementById('image-preview');
                    const previewImg = document.getElementById('preview-img');
                    
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        };
        
        // Drag and drop
        const formFileEnhanced = document.querySelector('.form-file-enhanced');
        
        formFileEnhanced.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#5dade2';
            this.style.background = '#f0f7ff';
        });
        
        formFileEnhanced.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.background = '#f7fafc';
        });
        
        formFileEnhanced.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.background = '#f7fafc';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                previewImage(fileInput);
            }
        });
        
        // Validation du formulaire
        form.addEventListener('submit', function(e) {
            const guestLimitInput = form.querySelector('input[name="guest_limit"]');
            
            if (guestLimitInput.value && parseInt(guestLimitInput.value) < 1) {
                e.preventDefault();
                guestLimitInput.classList.add('is-invalid');
                alert('Le nombre d\'invités doit être supérieur à 0');
                return false;
            }
            
            // Vérification de la taille de l'image (optionnel)
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('L\'image est trop volumineuse. Veuillez choisir une image de moins de 5MB.');
                    return false;
                }
            }
        });
        
        // Animation au focus
        const inputs = form.querySelectorAll('input');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    });
</script>
@endsection
