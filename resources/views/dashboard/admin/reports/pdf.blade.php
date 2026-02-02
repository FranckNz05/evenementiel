<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ \DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent' }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
            background: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #0f1a3d 0%, #1a237e 100%);
            color: white;
            padding: 20px;
            margin: -15mm -15mm 20px -15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            height: 60px;
            width: auto;
            background: white;
            padding: 8px;
            border-radius: 8px;
        }
        .header-info {
            display: flex;
            flex-direction: column;
        }
        .site-name {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 3px;
        }
        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: white;
        }
        .header-right {
            text-align: right;
            font-size: 10px;
            color: rgba(255,255,255,0.9);
        }
        .date-range {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px 15px;
            border-radius: 6px;
            margin: 15px 0 20px 0;
            border-left: 4px solid #0f1a3d;
            font-size: 11px;
            color: #374151;
        }
        .date-range strong {
            color: #0f1a3d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th {
            background: linear-gradient(135deg, #0f1a3d 0%, #1a237e 100%);
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #0a1229;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #f3f4f6;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .amount {
            font-weight: 600;
            color: #0f1a3d;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @php
                $logoPath = \DB::table('settings')->where('key', 'logo')->value('value');
                $siteName = \DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent';
                $logoFullPath = $logoPath ? (str_starts_with($logoPath, '/') ? $logoPath : '/' . $logoPath) : '/images/logo.png';
            @endphp
            @if(file_exists(public_path($logoFullPath)))
                <img src="{{ public_path($logoFullPath) }}" class="logo" alt="{{ $siteName }}">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="{{ $siteName }}">
            @endif
            <div class="header-info">
                <div class="site-name">{{ $siteName }}</div>
                <div class="report-title">{{ $title }}</div>
            </div>
        </div>
        <div class="header-right">
            Généré le {{ now()->format('d/m/Y') }}<br>
            {{ now()->format('H:i') }}
        </div>
    </div>

    <div class="date-range">
        <strong>📅 Période du rapport :</strong> {{ $dateRange }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">N°</th>
                @if($type == 'users')
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                @elseif($type == 'events')
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Organisateur</th>
                    <th>Date de début</th>
                    <th>Lieu</th>
                    <th>Prix</th>
                    <th>Statut</th>
                @elseif($type == 'orders')
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Événement</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                @elseif($type == 'payments')
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Événement</th>
                    <th>Montant</th>
                    <th>Méthode</th>
                    <th>Statut</th>
                    <th>Date</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @if($type == 'users')
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->prenom }} {{ $item->nom }}</strong></td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone ?? '—' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $item->email_verified_at ? 'badge-success' : 'badge-warning' }}">
                                {{ $item->email_verified_at ? 'Vérifié' : 'Non vérifié' }}
                            </span>
                        </td>
                    </tr>
                @elseif($type == 'events')
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->title }}</strong></td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td>{{ optional($item->organizer)->company_name ?? 'N/A' }}</td>
                        <td>{{ $item->start_date ? $item->start_date->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td>{{ $item->location ?? ($item->lieu ?? '—') }}</td>
                        <td class="amount">{{ $item->price ? number_format($item->price, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</td>
                        <td>
                            @if($item->is_published && $item->is_approved)
                                <span class="badge badge-success">Publié</span>
                            @elseif($item->is_published && !$item->is_approved)
                                <span class="badge badge-warning">En attente</span>
                            @else
                                <span class="badge badge-info">Brouillon</span>
                            @endif
                        </td>
                    </tr>
                @elseif($type == 'orders')
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->reference }}</strong></td>
                        <td>{{ ($item->user->prenom ?? '') . ' ' . ($item->user->nom ?? 'N/A') }}</td>
                        <td>{{ $item->event->title ?? 'N/A' }}</td>
                        <td class="amount">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge @if($item->statut == 'payé') badge-success @elseif($item->statut == 'en attente') badge-warning @else badge-danger @endif">
                                {{ ucfirst($item->statut) }}
                            </span>
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @elseif($type == 'payments')
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->reference_transaction ?? $item->matricule ?? 'N/A' }}</strong></td>
                        <td>{{ optional($item->user)->prenom }} {{ optional($item->user)->nom }}</td>
                        <td>{{ optional($item->event)->title ?? optional($item->order)->event->title ?? 'N/A' }}</td>
                        <td class="amount">{{ number_format($item->montant, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $item->methode_paiement }}</td>
                        <td>
                            <span class="badge @if($item->statut == 'payé' || $item->statut == 'paid') badge-success @elseif($item->statut == 'en attente' || $item->statut == 'pending') badge-warning @else badge-danger @endif">
                                {{ ucfirst($item->statut) }}
                            </span>
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="@if($type == 'events') 8 @elseif($type == 'payments') 8 @elseif($type == 'orders') 7 @else 6 @endif" class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <div>Aucune donnée disponible pour cette période</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-info">
            <div>Total d'enregistrements : <strong>{{ $data->count() }}</strong></div>
            <div>{{ $siteName }}</div>
        </div>
        <div style="margin-top: 5px;">
            Rapport généré le {{ now()->format('d/m/Y à H:i') }} | &copy; {{ date('Y') }} {{ $siteName }} - Tous droits réservés
        </div>
    </div>
</body>
</html>
