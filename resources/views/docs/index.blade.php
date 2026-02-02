@extends('layouts.app')

@section('title', 'Documentation MokiliEvent')

@section('content')
<div class="container py-4">
    <div class="page-header mb-4">
        <h1 class="page-title mb-0"><i class="fas fa-book me-2"></i>Documentation MokiliEvent</h1>
    </div>
    <p class="text-muted">Cette page centralise les principaux liens et fonctionnalités pour alimenter un chatbot et aider les utilisateurs.</p>

    <h3 class="mt-4">Liens publics</h3>
    <ul>
        <li><a href="{{ route('home') }}">Accueil</a> — Présentation et recherche</li>
        <li><a href="{{ url('/direct-events') }}">Tous les événements</a></li>
        <li><a href="{{ route('organizers.index') }}">Organisateurs</a></li>
        <li><a href="{{ route('about') }}">À propos</a></li>
        <li><a href="{{ route('contact') }}">Contact</a></li>
        <li><a href="{{ route('blogs.index') }}">Blog</a></li>
        <li><a href="{{ route('faq') }}">FAQ</a></li>
        <li><a href="{{ route('terms') }}">Conditions</a>, <a href="{{ route('privacy') }}">Confidentialité</a></li>
    </ul>

    <h3 class="mt-4">Événements personnalisés</h3>
    <ul>
        <li><a href="{{ route('custom-offers.index') }}">Formules personnalisées</a> — Start, Standard, Premium, Ultimate</li>
        <li>Paiement (simulation) — Airtel Money, MTN Mobile Money</li>
        <li><a href="{{ route('custom-events.index') }}">Tableau de bord personnalisé</a> — Historique + offres non utilisées</li>
        <li>Wizard de création: 
            <a href="{{ route('custom-events.wizard.step1') }}">Étape 1</a> → 
            <a href="{{ route('custom-events.wizard.step2') }}">Étape 2</a> → 
            <a href="{{ route('custom-events.wizard.step3') }}">Étape 3</a> → 
            Complete
        </li>
    </ul>

    <h3 class="mt-4">Achats et réservations</h3>
    <ul>
        <li>Réservations: <a href="{{ route('reservations.index') }}">Mes réservations</a></li>
        <li>Historique de paiements: <a href="{{ route('payments.history') }}">Mes paiements</a></li>
    </ul>

    <h3 class="mt-4">Pour les organisateurs</h3>
    <ul>
        <li>Devenir organisateur: <a href="{{ route('organizer.request.create') }}">Demander un compte</a></li>
        <li>Wizard événements classiques (organisateur): `events/wizard/step1…`</li>
    </ul>

    <h3 class="mt-4">Domaines</h3>
    <ul>
        <li><a href="https://www.mokilievent.com" target="_blank" rel="noopener">www.mokilievent.com</a> (domaine principal)</li>
    </ul>

    <h3 class="mt-4">Fonctionnalités clés</h3>
    <ul>
        <li>Billetterie, QR Scan, exports CSV/PDF (selon offre)</li>
        <li>Invitations SMS/WhatsApp (selon offre), rappel J-1 (Premium+)</li>
        <li>Suivi temps réel et statistiques (Standard+)</li>
        <li>Rôles et sécurité: Admin, Organisateur, Client</li>
    </ul>

    <h3 class="mt-4">Contact et Support</h3>
    <ul>
        <li>Email support: {{ DB::table('settings')->where('key', 'contact_email')->value('value') ?? 'contact@mokilievent.cg' }}</li>
        <li>Téléphone: {{ DB::table('settings')->where('key', 'phone_number')->value('value') ?? '+242 06 123 4567' }}</li>
    </ul>
</div>
@endsection


