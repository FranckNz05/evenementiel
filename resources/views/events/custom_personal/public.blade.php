<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des invités - {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .guest-list-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(0,0,0,0.07);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
        }
        .guest-list-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .guest-list-meta {
            text-align: center;
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .guest-table th, .guest-table td {
            text-align: center;
            vertical-align: middle;
        }
        .guest-table th {
            background: #f1f5f9;
            color: #495057;
            font-weight: 600;
        }
        .guest-table tr.cancelled {
            color: #aaa;
            background: #f8d7da;
        }
        @media (max-width: 600px) {
            .guest-list-container { padding: 1rem 0.2rem; }
            .guest-table th, .guest-table td { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
<div class="guest-list-container">
    <div class="guest-list-title">Liste des invités</div>
    <div class="guest-list-meta">
        <span><strong>{{ $event->title }}</strong></span><br>
        <span>{{ $event->start_date }} au {{ $event->end_date }} &mdash; {{ $event->location }}</span><br>
        <span id="public-url" class="d-block mt-2 small text-primary">URL d'accès : <span id="public-url-value">{{ url('/custom-personal-events/public/' . $event->url) }}</span></span>
    </div>
    <table class="table guest-table mb-0">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Tél</th>
                <th>Couple</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="guests-tbody">
            @foreach($event->guests as $guest)
                <tr class="@if($guest->status === 'cancelled') cancelled @endif">
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
                        @if($guest->status !== 'arrived')
                        <form method="POST" action="{{ route('custom-personal-events.guests.arrived', [$event->id, $guest->id]) }}" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Arrivé</button>
                        </form>
                        @endif
                        @if($guest->status !== 'cancelled')
                        <form method="POST" action="{{ route('custom-personal-events.guests.cancel', [$event->id, $guest->id]) }}" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
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
        })
        .listen('CustomPersonalEventUrlUpdated', (e) => {
            document.getElementById('public-url-value').textContent = `${window.location.origin}/custom-personal-events/public/${e.url}`;
        });

    function updateGuestList(guests) {
        const tbody = document.getElementById('guests-tbody');
        tbody.innerHTML = '';
        guests.forEach(function(guest) {
            let row = document.createElement('tr');
            row.className = guest.status === 'cancelled' ? 'cancelled' : '';
            let actions = '';
            if(guest.status !== 'arrived') {
                actions += `<form method='POST' action='/custom-personal-events/${eventId}/guests/${guest.id}/arrived' style='display:inline-block;'>`;
                actions += `<input type='hidden' name='_token' value='{{ csrf_token() }}'>`;
                actions += `<button type='submit' class='btn btn-sm btn-success'>Arrivé</button></form> `;
            }
            if(guest.status !== 'cancelled') {
                actions += `<form method='POST' action='/custom-personal-events/${eventId}/guests/${guest.id}/cancel' style='display:inline-block;'>`;
                actions += `<input type='hidden' name='_token' value='{{ csrf_token() }}'>`;
                actions += `<button type='submit' class='btn btn-sm btn-danger'>Annuler</button></form>`;
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
    }
</script>
</body>
</html>

