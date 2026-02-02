@extends('layouts.app')

@section('title', 'Prévisualisation du Billet')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Prévisualisation du Billet</h1>
                <div class="flex gap-3">
                    <a href="{{ route('tickets.download', $payment ?? 1) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Télécharger PDF
                    </a>
                    <a href="{{ route('tickets.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Retour
                    </a>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Note :</strong> Ceci est un aperçu de votre billet. Le PDF final aura une qualité optimale pour l'impression.
                </p>
            </div>
        </div>

        <!-- Prévisualisation du billet -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8 bg-gray-50">
                <div class="flex justify-center">
                    <div style="transform: scale(1.2); transform-origin: center;">
                        @include('tickets.template')
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations techniques</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Spécifications</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Dimensions : 170mm x 62mm</li>
                        <li>• Format : PDF haute qualité</li>
                        <li>• Résolution : 150 DPI</li>
                        <li>• Orientation : Paysage</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Sécurité</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• QR Code crypté unique</li>
                        <li>• Référence de sécurité</li>
                        <li>• Validation en temps réel</li>
                        <li>• Usage unique</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles spécifiques pour la prévisualisation */
    .preview-container {
        max-width: fit-content;
        margin: 0 auto;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .preview-container {
            transform: scale(0.8);
            transform-origin: center top;
        }
    }
    
    @media (max-width: 640px) {
        .preview-container {
            transform: scale(0.6);
        }
    }
</style>
@endsection