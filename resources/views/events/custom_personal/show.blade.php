@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2>{{ $event->title }} <small class="text-muted">({{ $event->category }})</small></h2>
    <p><strong>Date :</strong> {{ $event->start_date }} au {{ $event->end_date }}</p>
    <p><strong>Lieu :</strong> {{ $event->location }}</p>
    <p><strong>Message d'invitation :</strong> {{ $event->invitation_message ?? (auth()->user()->name . ' vous invite à ' . $event->title . ' le ' . $event->start_date . ' au ' . $event->location) }}</p>
    <div class="mb-3">
        <label class="form-label"><strong>URL publique :</strong></label>
        <div class="input-group" style="max-width:600px;">
            <input type="text" id="publicUrlInput" class="form-control" value="{{ url('/custom-personal-events/public/' . $event->url) }}" readonly>
            <input type="text" id="editCodeInput" class="form-control" value="{{ $event->url }}" style="max-width:180px;">
            <button class="btn btn-outline-primary" type="button" onclick="updateCode()">Modifier le code</button>
        </div>
        <div id="codeUpdateMsg" class="small mt-1"></div>
    </div>
    <hr>
    <h4>Liste des invités</h4>
    <div class="mb-3">
        <span class="badge bg-secondary me-2">Total : {{ $event->guests->count() }}</span>
        <span class="badge bg-success me-2">Arrivés : {{ $event->guests->where('status','arrived')->count() }}</span>
        <span class="badge bg-danger me-2">Annulés : {{ $event->guests->where('status','cancelled')->count() }}</span>
        <span class="badge bg-warning text-dark">En attente : {{ $event->guests->where('status','pending')->count() }}</span>
    </div>
    <div class="mb-3">
        <label class="form-label">Filtrer :</label>
        <select id="guestFilter" class="form-select" style="width:auto;display:inline-block;" onchange="filterGuests()">
            <option value="all">Tous</option>
            <option value="arrived">Arrivés</option>
            <option value="pending">En attente</option>
            <option value="cancelled">Annulés</option>
        </select>
    </div>
    <table class="table table-bordered" id="guests-table">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Couple</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->guests as $guest)
                <tr data-status="{{ $guest->status }}" @if($guest->status === 'cancelled') style="color: #aaa; background: #f8d7da;" @endif>
                    <td>{{ $guest->full_name }}</td>
                    <td>{{ $guest->email }}</td>
                    <td>{{ $guest->phone }}</td>
                    <td>{{ $guest->is_couple ? 'Oui' : 'Non' }}</td>
                    <td>
                        @if($guest->status === 'arrived')
                            <span class="badge bg-success">Arrivé</span>
                        @elseif($guest->status === 'cancelled')
                            <span class="badge bg-danger">Annulé</span>
                        @else
                            <span class="badge bg-secondary">En attente</span>
                        @endif
                    </td>
                    <td>
                        @if($guest->status !== 'cancelled')
                        <form method="POST" action="{{ route('custom-personal-events.guests.cancel', [$event->id, $guest->id]) }}" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                        </form>
                        @endif
                        @if($guest->status !== 'arrived' && $guest->status !== 'cancelled')
                        <form method="POST" action="{{ route('custom-personal-events.guests.arrived', [$event->id, $guest->id]) }}" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Arrivé</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <h5>Ajouter un invité</h5>
    <form method="POST" action="{{ route('custom-personal-events.guests.add', $event->id) }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <input type="text" name="full_name" class="form-control" placeholder="Nom complet" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="phone" class="form-control" placeholder="Téléphone">
        </div>
        <div class="col-md-2">
            <select name="is_couple" class="form-control">
                <option value="0">Individuel</option>
                <option value="1">Couple</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateCode() {
    const newCode = document.getElementById('editCodeInput').value.trim();
    if (!newCode.match(/^[a-zA-Z0-9_-]{6,32}$/)) {
        document.getElementById('codeUpdateMsg').innerHTML = '<span class="text-danger">Le code doit faire 6 à 32 caractères, lettres, chiffres, tirets ou underscores.</span>';
        return;
    }
    fetch(`{{ route('custom-personal-events.update-code', $event->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ url: newCode })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('publicUrlInput').value = `${window.location.origin}/custom-personal-events/public/${newCode}`;
            document.getElementById('codeUpdateMsg').innerHTML = '<span class="text-success">Code mis à jour !</span>';
        } else {
            document.getElementById('codeUpdateMsg').innerHTML = '<span class="text-danger">'+(data.message||'Erreur lors de la mise à jour')+'</span>';
        }
    })
    .catch(() => {
        document.getElementById('codeUpdateMsg').innerHTML = '<span class="text-danger">Erreur lors de la mise à jour</span>';
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/ably@1.2.13/browser/static/ably.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>
<script>
    window.Ably = Ably;
    window.Echo = new Echo({
        broadcaster: 'ably',
        key: '{{ env('ABLY_KEY') }}',
    });
    const eventId = @json($event->id);
    window.Echo.private('custom-personal-event.' + eventId)
        .listen('CustomPersonalEventGuestUpdated', (e) => {
            updateGuestList(e.guests);
        });

    function updateGuestList(guests) {
        const tbody = document.querySelector('#guests-table tbody');
        tbody.innerHTML = '';
        guests.forEach(function(guest) {
            let row = document.createElement('tr');
            row.setAttribute('data-status', guest.status);
            if(guest.status === 'cancelled') row.style = 'color: #aaa; background: #f8d7da;';
            let actions = '';
            if(guest.status !== 'cancelled') {
                actions += `<form method='POST' action='${window.location.pathname}/guests/${guest.id}/cancel' style='display:inline-block;'>@csrf<button type='submit' class='btn btn-sm btn-danger'>Annuler</button></form> `;
            }
            if(guest.status !== 'arrived' && guest.status !== 'cancelled') {
                actions += `<form method='POST' action='${window.location.pathname}/guests/${guest.id}/arrived' style='display:inline-block;'>@csrf<button type='submit' class='btn btn-sm btn-success'>Arrivé</button></form>`;
            }
            row.innerHTML = `
                <td>${guest.full_name}</td>
                <td>${guest.email}</td>
                <td>${guest.phone ?? ''}</td>
                <td>${guest.is_couple ? 'Oui' : 'Non'}</td>
                <td>
                    ${guest.status === 'arrived' ? '<span class="badge bg-success">Arrivé</span>' : guest.status === 'cancelled' ? '<span class="badge bg-danger">Annulé</span>' : '<span class="badge bg-secondary">En attente</span>'}
                </td>
                <td>${actions}</td>
            `;
            tbody.appendChild(row);
        });
        filterGuests();
    }

    function filterGuests() {
        const filter = document.getElementById('guestFilter').value;
        const rows = document.querySelectorAll('#guests-table tbody tr');
        rows.forEach(row => {
            if (filter === 'all' || row.getAttribute('data-status') === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush
