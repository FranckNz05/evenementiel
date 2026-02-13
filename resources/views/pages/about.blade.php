@extends('layouts.app')

@section('title', 'À propos de MokiliEvent')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4 text-bleu-nuit">À propos de MokiliEvent</h1>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">Notre Mission</h2>
                    <p class="mb-4">
                        MokiliEvent est une plateforme dédiée à la promotion et à la gestion d'événements en République du Congo. 
                        Notre mission est de faciliter l'organisation et la participation aux événements culturels, sportifs, 
                        professionnels et sociaux à travers le pays.
                    </p>

                    <h2 class="h4 mb-3">Notre Vision</h2>
                    <p class="mb-4">
                        Nous aspirons à devenir la référence en matière de gestion d'événements en RDC, 
                        en offrant une plateforme innovante qui connecte les organisateurs d'événements 
                        avec leur public, tout en simplifiant le processus de billetterie et de gestion.
                    </p>

                    <h2 class="h4 mb-3">Nos Valeurs</h2>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-blanc-or me-2"></i>
                            Innovation et excellence dans le service
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-blanc-or me-2"></i>
                            Accessibilité et facilité d'utilisation
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-blanc-or me-2"></i>
                            Sécurité et fiabilité des transactions
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-blanc-or me-2"></i>
                            Support client réactif et professionnel
                        </li>
                    </ul>

                    <h2 class="h4 mb-3">Notre Équipe</h2>
                    <p class="mb-4">
                        Notre équipe est composée de professionnels passionnés par les événements et les nouvelles technologies. 
                        Nous travaillons ensemble pour offrir la meilleure expérience possible à nos utilisateurs, 
                        qu'il s'agisse des organisateurs d'événements ou des participants.
                    </p>

                    <div class="text-center mt-4">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Contactez-nous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
