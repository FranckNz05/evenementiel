@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2>Mes événements personnalisés</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('custom-personal-events.create') }}" class="btn btn-primary mb-3">Créer un nouvel événement</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Lieu</th>
                <th>Invités</th>
                <th>URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->category }}</td>
                    <td>{{ $event->start_date }}</td>
                    <td>{{ $event->end_date }}</td>
                    <td>{{ $event->location }}</td>
                    <td>{{ $event->guests->count() }}</td>
                    <td>
                        <a href="{{ url('/custom-personal-events/public/' . $event->url) }}" target="_blank">Lien public</a>
                    </td>
                    <td>
                        <a href="{{ route('custom-personal-events.show', $event->url) }}" class="btn btn-sm btn-info">Gérer</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Aucun événement personnalisé trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
