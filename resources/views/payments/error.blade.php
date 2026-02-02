@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#f44336" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <h2 class="mb-3">Erreur de paiement</h2>
                    <p class="lead">{{ $message ?? 'Une erreur est survenue lors du traitement de votre paiement.' }}</p>
                    <p>Veuillez réessayer ou contacter notre service client si le problème persiste.</p>
                    
                    <div class="mt-4">
                        <a href="{{ url('/direct-events') }}" class="btn btn-primary">
                            Découvrir des événements
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-headset me-2"></i>Contactez-nous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection