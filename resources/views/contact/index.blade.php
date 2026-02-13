@extends('layouts.app')

@section('title', 'Contactez-nous')

@section('content')

<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-or-fonce: #f8fafc;
}

.contact-info-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
}

.contact-info-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    border-color: var(--blanc-or);
}

.contact-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white !important;
    font-size: 2rem;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
}

.contact-icon i,
.contact-icon .fas,
.contact-icon .fa {
    color: white !important;
}

.contact-icon.yellow {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
}

.contact-icon.yellow i,
.contact-icon.yellow .fas,
.contact-icon.yellow .fa {
    color: white !important;
}

.contact-info-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
}

.contact-info-text {
    color: #666;
    line-height: 1.7;
    margin-bottom: 0.5rem;
}

.contact-info-link {
    color: var(--bleu-nuit);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.contact-info-link:hover {
    color: var(--bleu-nuit-clair);
    text-decoration: none;
}

.contact-form {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.contact-form-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 0.2rem rgba(15, 26, 61, 0.25);
    outline: none;
}

.btn-submit {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    border: none;
    padding: 0.875rem 2.5rem;
    font-size: 1.125rem;
    font-weight: 700;
    border-radius: 50px;
    transition: all 0.3s ease;
    width: 100%;
    text-decoration: none !important;
}

.btn-submit:hover,
.btn-submit:focus,
.btn-submit:active {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(15, 26, 61, 0.3);
    background: linear-gradient(135deg, var(--bleu-nuit-clair) 0%, var(--bleu-nuit) 100%);
    color: white;
    text-decoration: none !important;
}

.btn-submit i {
    color: white !important;
}

.map-container {
    background: white;
    border-radius: 16px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.map-container iframe {
    width: 100%;
    height: 400px;
    border-radius: 12px;
    border: none;
}

.social-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
}

.social-link {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background: linear-gradient(135deg, var(--bleu-nuit-clair) 0%, var(--bleu-nuit) 100%);
    color: #ffffff;
    transform: translateY(-5px) scale(1.1);
}

/* Spinner Bootstrap personnalisé */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
    border-color: white;
    border-right-color: transparent;
}

@media (max-width: 768px) {
    .contact-form {
        padding: 1.5rem;
    }
}
</style>

<!-- Contact Info Cards -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="contact-info-title">Notre Adresse</h3>
                    <p class="contact-info-text">
                        {{ DB::table('settings')->where('key', 'address')->value('value') ?? 'Brazzaville, République du Congo' }}
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-icon yellow">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3 class="contact-info-title">Téléphone</h3>
                    @php
                        $phone = DB::table('settings')->where('key', 'phone_number')->value('value');
                        $phones = explode(',', $phone ?? '+242 06 408 8868');
                    @endphp
                    @foreach($phones as $tel)
                        <p class="contact-info-text">
                            <a href="tel:{{ trim($tel) }}" class="contact-info-link">{{ trim($tel) }}</a>
                        </p>
                    @endforeach
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="contact-info-title">Email</h3>
                    <p class="contact-info-text">
                        <a href="mailto:{{ DB::table('settings')->where('key', 'contact_email')->value('value') ?? 'contact@mokilievent.cg' }}" class="contact-info-link">
                            {{ DB::table('settings')->where('key', 'contact_email')->value('value') ?? 'contact@mokilievent.cg' }}
                        </a>
                    </p>
                    <div class="social-links">
                        @if(DB::table('settings')->where('key', 'facebook_url')->value('value'))
                        <a href="{{ DB::table('settings')->where('key', 'facebook_url')->value('value') }}" 
                           class="social-link" target="_blank" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if(DB::table('settings')->where('key', 'twitter_url')->value('value'))
                        <a href="{{ DB::table('settings')->where('key', 'twitter_url')->value('value') }}" 
                           class="social-link" target="_blank" aria-label="Twitter">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        @endif
                        @if(DB::table('settings')->where('key', 'instagram_url')->value('value'))
                        <a href="{{ DB::table('settings')->where('key', 'instagram_url')->value('value') }}" 
                           class="social-link" target="_blank" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if(DB::table('settings')->where('key', 'linkedin_url')->value('value'))
                        <a href="{{ DB::table('settings')->where('key', 'linkedin_url')->value('value') }}" 
                           class="social-link" target="_blank" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form and Map -->
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form">
                    <h2 class="contact-form-title">
                        <i class="fas fa-paper-plane me-2" style="color: var(--bleu-nuit);"></i>
                        Envoyez-nous un message
                    </h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="Votre nom complet">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required
                                       placeholder="votre@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('subject') is-invalid @enderror" 
                                       id="subject" 
                                       name="subject" 
                                       value="{{ old('subject') }}" 
                                       required
                                       placeholder="Objet de votre message">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          required
                                          placeholder="Décrivez votre demande ou votre question...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="mb-3" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; border-left: 3px solid var(--bleu-nuit);">
                                    <small style="color: #666; line-height: 1.6;">
                                        <i class="fas fa-shield-alt me-2" style="color: var(--bleu-nuit-clair);"></i>
                                        <strong>Protection de vos données :</strong> Les informations collectées (nom, email, sujet, message) 
                                        sont utilisées uniquement pour répondre à votre demande. Elles sont conservées pendant 3 ans maximum 
                                        et ne sont jamais vendues à des tiers. Conformément à la loi congolaise sur la protection des données 
                                        personnelles, vous disposez d'un droit d'accès, de rectification et de suppression de vos données. 
                                        <a href="{{ route('privacy') }}" style="color: var(--bleu-nuit-clair); font-weight: 600;">En savoir plus</a>
                                    </small>
                                </div>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map -->
            <div class="col-lg-5">
                <div class="map-container">
                    <h3 class="contact-info-title mb-3">
                        <i class="fas fa-map me-2" style="color: var(--bleu-nuit);"></i>
                        Localisation
                    </h3>
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63520.110523304214!2d15.241595895363882!3d-4.267459108480173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a6a33d43d88f31d%3A0x2a91a34b7c76a5d0!2sBrazzaville%2C%20Congo!5e0!3m2!1sfr!2sfr!4v1647524145548!5m2!1sfr!2sfr"
                        frameborder="0" 
                        allowfullscreen="" 
                        aria-hidden="false"
                        tabindex="0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = form.querySelector('.btn-submit');
    
    form.addEventListener('submit', function(e) {
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Envoi en cours...';
        
        // Réactiver après 5 secondes au cas où il y aurait une erreur
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 5000);
    });
});
</script>
@endpush
