<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ \DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent' }}</title>
    <style>
        /* ===============================================
           SYSTÈME DE DESIGN - BLEU NUIT & BLANC
           Design Premium pour Rapports PDF
           =============================================== */
        
        @page {
            margin: 20mm 15mm 15mm 15mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
            background: #ffffff;
        }
        
        /* ===============================================
           HEADER - BLEU NUIT
           =============================================== */
        
        .header {
            background: linear-gradient(135deg, #0f1a3d 0%, #1a237e 100%);
            color: white;
            padding: 20px 25px;
            margin: -20mm -15mm 20px -15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
            border-bottom: 3px solid #ffffff;
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
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .header-info {
            display: flex;
            flex-direction: column;
        }
        
        .site-name {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: rgba(255,255,255,0.95);
        }
        
        .header-right {
            text-align: right;
            font-size: 10px;
            color: rgba(255,255,255,0.9);
            line-height: 1.6;
            padding: 8px 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
        }
        
        /* ===============================================
           DATE RANGE - FILTRE
           =============================================== */
        
        .date-range {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 14px 18px;
            border-radius: 8px;
            margin: 15px 0 25px 0;
            border-left: 6px solid #0f1a3d;
            font-size: 11px;
            color: #1f2937;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-range strong {
            color: #0f1a3d;
            font-weight: 700;
        }
        
        .date-range-icon {
            font-size: 14px;
            background: #0f1a3d;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* ===============================================
           TABLEAU - DESIGN ÉPURÉ
           =============================================== */
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background: linear-gradient(135deg, #0f1a3d 0%, #1a237e 100%);
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: none;
            white-space: nowrap;
        }
        
        th:first-child {
            padding-left: 15px;
        }
        
        th:last-child {
            padding-right: 15px;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            color: #374151;
            vertical-align: middle;
        }
        
        td:first-child {
            padding-left: 15px;
        }
        
        td:last-child {
            padding-right: 15px;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        /* ===============================================
           NUMÉRO DE LIGNE
           =============================================== */
        
        .row-number {
            display: inline-block;
            font-weight: 700;
            color: #0f1a3d;
            background: #e8eaf6;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 6px;
            font-size: 10px;
        }
        
        /* ===============================================
           BADGES DE STATUT
           =============================================== */
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }
        
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
            border-color: #3b82f6;
        }
        
        .badge-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
            border-color: #9ca3af;
        }
        
        /* ===============================================
           MONTANTS
           =============================================== */
        
        .amount {
            font-weight: 700;
            color: #0f1a3d;
            font-size: 11px;
        }
        
        .amount-currency {
            font-size: 9px;
            color: #6b7280;
            margin-left: 2px;
        }
        
        /* ===============================================
           EMPTY STATE
           =============================================== */
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .empty-state-description {
            font-size: 11px;
            color: #6b7280;
        }
        
        /* ===============================================
           FOOTER
           =============================================== */
        
        .footer {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        
        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 0 5px;
        }
        
        .footer-info strong {
            color: #0f1a3d;
            font-weight: 700;
        }
        
        .footer-logo {
            font-weight: 700;
            color: #0f1a3d;
        }
        
        /* ===============================================
           UTILITAIRES
           =============================================== */
        
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 600; }
        
        .text-primary { color: #0f1a3d; }
        .text-success { color: #065f46; }
        .text-warning { color: #92400e; }
        .text-danger { color: #991b1b; }
        
        .bg-light { background-color: #f9fafb; }
        
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        
        .p-2 { padding: 10px; }
        .p-3 { padding: 15px; }
        
        /* ===============================================
           RESPONSIVE
           =============================================== */
        
        @media print {
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .date-range {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .row-number {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER - BLEU NUIT -->
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
            @else
                <div style="background: white; padding: 8px 15px; border-radius: 8px;">
                    <span style="color: #0f1a3d; font-weight: 700; font-size: 20px;">{{ $siteName }}</span>
                </div>
            @endif
            <div class="header-info">
                <div class="site-name">{{ $siteName }}</div>
                <div class="report-title">{{ $title }}</div>
            </div>
        </div>
        <div class="header-right">
            <div style="font-weight: 700; margin-bottom: 2px;">{{ now()->format('d/m/Y') }}</div>
            <div>{{ now()->format('H:i') }}</div>
            <div style="margin-top: 5px; font-size: 9px; opacity: 0.8;">Réf: RPT-{{ now()->format('Ymd') }}</div>
        </div>
    </div>

    <!-- PÉRIODE DU RAPPORT -->
    <div class="date-range">
        <span class="date-range-icon">📅</span>
        <div>
            <strong>Période du rapport :</strong> {{ $dateRange }}
            @if(isset($totalCount) && $totalCount > 0)
                <span style="margin-left: 10px; padding-left: 10px; border-left: 1px solid #d1d5db;">
                    <strong>Total :</strong> {{ number_format($totalCount ?? $data->count(), 0, ',', ' ') }} enregistrement(s)
                </span>
            @endif
        </div>
    </div>

    <!-- TABLEAU PRINCIPAL -->
    <table>
        <thead>
            <tr>
                <th style="width: 45px; text-align: center;">#</th>
                @if($type == 'users')
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Inscription</th>
                    <th style="text-align: center;">Statut</th>
                @elseif($type == 'events')
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Organisateur</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th style="text-align: right;">Prix</th>
                    <th style="text-align: center;">Statut</th>
                @elseif($type == 'orders')
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Événement</th>
                    <th style="text-align: right;">Montant</th>
                    <th style="text-align: center;">Statut</th>
                    <th>Date</th>
                @elseif($type == 'payments')
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Événement</th>
                    <th style="text-align: right;">Montant</th>
                    <th>Méthode</th>
                    <th style="text-align: center;">Statut</th>
                    <th>Date</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @if($type == 'users')
                    <tr>
                        <td style="text-align: center;">
                            <span class="row-number">{{ $loop->iteration }}</span>
                        </td>
                        <td><span style="font-weight: 600; color: #0f1a3d;">{{ $item->prenom }} {{ $item->nom }}</span></td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone ?? '—' }}</td>
                        <td>{{ $item->created_at instanceof \Carbon\Carbon ? $item->created_at->format('d/m/Y') : date('d/m/Y', strtotime($item->created_at)) }}</td>
                        <td style="text-align: center;">
                            @if($item->email_verified_at)
                                <span class="badge badge-success">
                                    <span style="margin-right: 3px;">✓</span> Vérifié
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <span style="margin-right: 3px;">⏳</span> Non vérifié
                                </span>
                            @endif
                        </td>
                    </tr>
                @elseif($type == 'events')
                    <tr>
                        <td style="text-align: center;">
                            <span class="row-number">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f1a3d;">{{ $item->title }}</span>
                            @if(isset($item->is_featured) && $item->is_featured)
                                <span style="margin-left: 5px; color: #f59e0b;">★</span>
                            @endif
                        </td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td>{{ optional($item->organizer)->company_name ?? optional($item->user)->nom ?? 'N/A' }}</td>
                        <td>{{ $item->start_date ? ($item->start_date instanceof \Carbon\Carbon ? $item->start_date->format('d/m/Y H:i') : date('d/m/Y H:i', strtotime($item->start_date))) : 'N/A' }}</td>
                        <td>{{ $item->location ?? ($item->lieu ?? $item->ville ?? '—') }}</td>
                        <td style="text-align: right;">
                            @if($item->price > 0)
                                <span class="amount">{{ number_format($item->price, 0, ',', ' ') }}</span>
                                <span class="amount-currency">FCFA</span>
                            @else
                                <span class="badge badge-secondary">Gratuit</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if(isset($item->etat))
                                @if($item->etat == 'En cours' || $item->etat == 'published')
                                    <span class="badge badge-success">Actif</span>
                                @elseif($item->etat == 'En attente' || $item->etat == 'pending')
                                    <span class="badge badge-warning">En attente</span>
                                @elseif($item->etat == 'Archivé' || $item->etat == 'archived')
                                    <span class="badge badge-secondary">Archivé</span>
                                @elseif($item->etat == 'Annulé' || $item->etat == 'cancelled')
                                    <span class="badge badge-danger">Annulé</span>
                                @else
                                    <span class="badge badge-info">{{ $item->etat }}</span>
                                @endif
                            @else
                                @if($item->is_published && $item->is_approved)
                                    <span class="badge badge-success">Publié</span>
                                @elseif($item->is_published && !$item->is_approved)
                                    <span class="badge badge-warning">En attente</span>
                                @else
                                    <span class="badge badge-info">Brouillon</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @elseif($type == 'orders')
                    <tr>
                        <td style="text-align: center;">
                            <span class="row-number">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-weight: 600; color: #0f1a3d;">{{ $item->reference ?? $item->numero_commande ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($item->user)
                                <span style="font-weight: 600;">{{ $item->user->prenom ?? '' }} {{ $item->user->nom ?? '' }}</span>
                                <div style="font-size: 8px; color: #6b7280;">{{ $item->user->email ?? '' }}</div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $item->event->title ?? $item->evenement->title ?? 'N/A' }}</td>
                        <td style="text-align: right;">
                            <span class="amount">{{ number_format($item->total ?? $item->montant_total ?? $item->amount ?? 0, 0, ',', ' ') }}</span>
                            <span class="amount-currency">FCFA</span>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $status = strtolower($item->statut ?? $item->status ?? '');
                            @endphp
                            @if(in_array($status, ['payé', 'paid', 'paye', 'success', 'completed']))
                                <span class="badge badge-success">Payé</span>
                            @elseif(in_array($status, ['en attente', 'pending', 'waiting']))
                                <span class="badge badge-warning">En attente</span>
                            @elseif(in_array($status, ['échoué', 'failed', 'error']))
                                <span class="badge badge-danger">Échoué</span>
                            @else
                                <span class="badge badge-secondary">{{ $item->statut ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at ? ($item->created_at instanceof \Carbon\Carbon ? $item->created_at->format('d/m/Y H:i') : date('d/m/Y H:i', strtotime($item->created_at))) : 'N/A' }}</td>
                    </tr>
                @elseif($type == 'payments')
                    <tr>
                        <td style="text-align: center;">
                            <span class="row-number">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-weight: 600; color: #0f1a3d;">{{ $item->reference_transaction ?? $item->matricule ?? $item->numero_transaction ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($item->user)
                                <span style="font-weight: 600;">{{ $item->user->prenom ?? '' }} {{ $item->user->nom ?? '' }}</span>
                                <div style="font-size: 8px; color: #6b7280;">{{ $item->user->email ?? '' }}</div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ optional($item->event)->title ?? optional($item->order)->event->title ?? 'N/A' }}</td>
                        <td style="text-align: right;">
                            <span class="amount">{{ number_format($item->montant ?? $item->amount ?? 0, 0, ',', ' ') }}</span>
                            <span class="amount-currency">FCFA</span>
                        </td>
                        <td>
                            @php
                                $method = $item->methode_paiement ?? $item->mode_paiement ?? $item->payment_method ?? '';
                            @endphp
                            @if(stripos($method, 'airtel') !== false)
                                <span style="font-weight: 600; color: #dc2626;">Airtel Money</span>
                            @elseif(stripos($method, 'mtn') !== false)
                                <span style="font-weight: 600; color: #d97706;">MTN Money</span>
                            @else
                                {{ $method ?: 'N/A' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @php
                                $status = strtolower($item->statut ?? $item->status ?? '');
                            @endphp
                            @if(in_array($status, ['payé', 'paid', 'paye', 'success', 'completed']))
                                <span class="badge badge-success">Payé</span>
                            @elseif(in_array($status, ['en attente', 'pending', 'waiting']))
                                <span class="badge badge-warning">En attente</span>
                            @elseif(in_array($status, ['échoué', 'failed', 'error']))
                                <span class="badge badge-danger">Échoué</span>
                            @else
                                <span class="badge badge-secondary">{{ $item->statut ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at ? ($item->created_at instanceof \Carbon\Carbon ? $item->created_at->format('d/m/Y H:i') : date('d/m/Y H:i', strtotime($item->created_at))) : 'N/A' }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="
                        @if($type == 'events') 8 
                        @elseif($type == 'payments') 8 
                        @elseif($type == 'orders') 7 
                        @else 6 
                        @endif" 
                        class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <div class="empty-state-title">Aucune donnée disponible</div>
                        <div class="empty-state-description">Aucun enregistrement trouvé pour la période sélectionnée</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- RÉCAPITULATIF ET STATISTIQUES -->
    @if($data->count() > 0)
    <div style="margin-top: 25px; display: flex; justify-content: space-between;">
        <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #0f1a3d; width: 48%;">
            <div style="font-weight: 700; color: #0f1a3d; margin-bottom: 8px; font-size: 11px;">RÉCAPITULATIF</div>
            <table style="width: 100%; border: none; box-shadow: none; margin: 0;">
                <tr style="background: none;">
                    <td style="padding: 5px 0; border: none;">Total enregistrements</td>
                    <td style="padding: 5px 0; border: none; text-align: right; font-weight: 700;">{{ number_format($data->count(), 0, ',', ' ') }}</td>
                </tr>
                @if($type == 'orders' || $type == 'payments')
                <tr style="background: none;">
                    <td style="padding: 5px 0; border: none;">Montant total</td>
                    <td style="padding: 5px 0; border: none; text-align: right; font-weight: 700; color: #0f1a3d;">
                        @php
                            $total = $data->sum(function($item) {
                                return $item->montant ?? $item->total ?? $item->amount ?? 0;
                            });
                        @endphp
                        {{ number_format($total, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @endif
            </table>
        </div>
        <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #0f1a3d; width: 48%;">
            <div style="font-weight: 700; color: #0f1a3d; margin-bottom: 8px; font-size: 11px;">INFORMATIONS</div>
            <div style="font-size: 10px; color: #4b5563;">
                Rapport généré le {{ now()->format('d/m/Y à H:i') }}<br>
                Période : {{ $dateRange }}
            </div>
        </div>
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-info">
            <div>
                <span class="footer-logo">{{ $siteName }}</span>
            </div>
            <div>
                <strong>{{ $data->count() }}</strong> enregistrement(s) • 
                Période: {{ $dateRange }}
            </div>
        </div>
        <div style="margin-top: 5px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
            Rapport généré le {{ now()->format('d/m/Y à H:i') }} | 
            &copy; {{ date('Y') }} {{ $siteName }} - Tous droits réservés
        </div>
    </div>
</body>
</html>