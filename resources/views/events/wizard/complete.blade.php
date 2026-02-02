@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3">Événement créé avec succès !</h2>
                    <p class="lead mb-4">
                        Votre événement a été créé et est en attente de validation par un admin.
                        Vous recevrez une notification dès qu'il sera approuvé et publié.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('events.wizard.step1') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus-circle me-1"></i> Créer un autre événement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


