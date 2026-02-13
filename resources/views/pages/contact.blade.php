@extends('layouts.app')

@section('title', 'Contactez-nous')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4 text-bleu-nuit">Contactez-nous</h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h2 class="h4 mb-3">Informations de contact</h2>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt text-blanc-or me-2"></i>
                                    <strong>Adresse :</strong><br>
                                    Kinshasa, RDC
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone text-blanc-or me-2"></i>
                                    <strong>Téléphone :</strong><br>
                                    +243 123 456 789
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope text-blanc-or me-2"></i>
                                    <strong>Email :</strong><br>
                                    contact@mokilievent.com
                                </li>
                            </ul>

                            <h2 class="h4 mb-3 mt-4">Suivez-nous</h2>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-bleu-nuit fs-4">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" class="text-bleu-nuit fs-4">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-bleu-nuit fs-4">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="text-bleu-nuit fs-4">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h2 class="h4 mb-3">Envoyez-nous un message</h2>
                            <form action="{{ route('contact.submit') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Sujet *</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.contact-hero {
    background: linear-gradient(rgba(15, 26, 61, 0.7), rgba(15, 26, 61, 0.7)),
                url('https://images.unsplash.com/photo-1531058020387-3be344556be6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center;
    padding: 100px 0;
    position: relative;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
}

.contact-info-card {
    transition: all 0.3s ease;
    border-left: 4px solid var(--bs-primary);
}

.contact-info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.social-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-floating label {
    color: #6c757d;
}

.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(15, 26, 61, 0.25);
}

.btn-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.btn-primary:hover {
    background-color: #0a1640;
    border-color: #0a1640;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation for form elements
    const formElements = document.querySelectorAll('.form-floating');
    formElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = 1;
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const consent = document.getElementById('consent');
            if (!consent.checked) {
                e.preventDefault();
                alert('Veuillez accepter les conditions de traitement des données.');
            }
        });
    }
});
</script>
@endpush
