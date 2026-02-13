@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row items-start gap-8">
            <!-- Profile Image -->
            <div class="w-full md:w-1/3">
                <div class="relative">
                    <img src="{{ $organizer->logoUrl }}"
                         alt="{{ $organizer->company_name }}"
                         class="w-full rounded-lg shadow-md">
                </div>
            </div>

            <!-- Profile Info -->
            <div class="w-full md:w-2/3">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $organizer->name }}</h1>

                @if($organizer->bio)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">À propos</h2>
                    <p class="text-gray-600">{{ $organizer->bio }}</p>
                </div>
                @endif

                <!-- Social Links -->
                @if($organizer->socialLinks->count() > 0)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-3">Réseaux sociaux</h2>
                    <div class="flex flex-wrap gap-4">
                        @foreach($organizer->socialLinks as $link)
                        <a href="{{ $link->url }}"
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                            <i class="fab fa-{{ strtolower($link->platform) }}"></i>
                            <span>{{ ucfirst($link->platform) }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-gray-800">{{ $organizer->events_count }}</span>
                        <span class="text-gray-600">Événements</span>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-gray-800">{{ $organizer->total_tickets_sold }}</span>
                        <span class="text-gray-600">Billets vendus</span>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-gray-800">{{ $organizer->followers_count }}</span>
                        <span class="text-gray-600">Abonnés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Section -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Événements organisés</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizer->events as $event)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('storage/' . $event->image_path) }}"
                         alt="{{ $event->title }}"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 mb-2">{{ Str::limit($event->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">{{ $event->start_date->format('d M Y') }}</span>
                            <a href="{{ route('events.show', $event) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Voir plus
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

