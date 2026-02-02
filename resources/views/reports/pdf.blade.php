<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            color: #4e73df;
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-style: italic;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="date-range">Période : {{ $dateRange }}</div>
    </div>

    @if(count($data) > 0)
        @if($type == 'events')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Organisateur</th>
                        <th>Date de début</th>
                        <th>Ville</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $event)
                        <tr>
                            <td>{{ $event->id }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->category->name ?? 'Non catégorisé' }}</td>
                            <td>{{ $event->organizer->company_name ?? 'Non spécifié' }}</td>
                            <td>{{ $event->start_date ? $event->start_date->format('d/m/Y H:i') : 'Non spécifié' }}</td>
                            <td>{{ $event->ville ?? 'Non spécifié' }}</td>
                            <td>{{ $event->is_published ? 'Publié' : 'Brouillon' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($type == 'users')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->prenom }} {{ $user->nom }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'Non spécifié' }}</td>
                            <td>{{ $user->roles->pluck('name')->implode(', ') ?? 'Utilisateur' }}</td>
                            <td>{{ $user->is_active ? 'Actif' : 'Inactif' }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($type == 'orders')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matricule</th>
                        <th>Client</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Date de reservation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->matricule ?? 'Non spécifié' }}</td>
                            <td>{{ $order->user ? ($order->user->prenom . ' ' . $order->user->nom) : 'Utilisateur inconnu' }}</td>
                            <td>{{ number_format($order->montant_total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $order->statut }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($type == 'payments')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Statut</th>
                        <th>Date de paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->reference ?? 'Non spécifié' }}</td>
                            <td>{{ $payment->order && $payment->order->user ? ($payment->order->user->prenom . ' ' . $payment->order->user->nom) : 'Utilisateur inconnu' }}</td>
                            <td>{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $payment->methode_paiement ?? 'Non spécifié' }}</td>
                            <td>{{ $payment->statut }}</td>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <p>Aucune donnée disponible pour cette période.</p>
    @endif

    <div class="footer">
        <p>Rapport généré le {{ now()->format('d/m/Y H:i') }} | MokiliEvent</p>
    </div>
</body>
</html>