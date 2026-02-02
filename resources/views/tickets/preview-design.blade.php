<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévisualisation Design Billet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #1a1a2e;
            padding: 20px;
            min-height: 100vh;
        }
        
        .preview-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .preview-header h1 {
            color: white;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .preview-header p {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }
        
        .preview-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #ffffff;
            color: #1a1a2e;
        }
        
        .btn-primary:hover {
            background: #FFC700;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .test-badge {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .ticket-wrapper {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        
        .info-panel {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            padding: 25px;
            border-radius: 15px;
            margin-top: 20px;
            color: white;
        }
        
        .info-panel h3 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #ffffff;
        }
        
        .info-item label {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
        }
        
        .info-item value {
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .instructions {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            color: white;
        }
        
        .instructions h4 {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .instructions ul {
            margin-left: 20px;
            line-height: 1.8;
        }
        
        .instructions code {
            background: rgba(0,0,0,0.2);
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- En-tête -->
        <div class="preview-header">
            <h1>
                🎨 Prévisualisation Design Billet
                @if($isTestData)
                    <span class="test-badge">DONNÉES DE TEST</span>
                @endif
            </h1>
            <p>Modifiez le fichier <code>resources/views/tickets/template.blade.php</code> et actualisez cette page pour voir vos changements</p>
            
            <div class="preview-actions">
                <button class="btn btn-primary" onclick="location.reload()">
                    🔄 Actualiser
                </button>
                <a href="{{ route('ticket.design.pdf') }}{{ $payment->id ? '?payment_id=' . $payment->id : '' }}" class="btn btn-primary" target="_blank">
                    📄 Télécharger PDF
                </a>
                <a href="{{ route('ticket.design.preview') }}" class="btn btn-secondary">
                    🔀 Données de test
                </a>
                @if(!$isTestData && auth()->check())
                    <a href="{{ route('payments.history') }}" class="btn btn-secondary">
                        📋 Mes paiements
                    </a>
                @endif
            </div>
        </div>

        <!-- Informations du billet -->
        <div class="info-panel">
            <h3>📊 Informations du billet</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Événement</label>
                    <value>{{ $event->title }}</value>
                </div>
                <div class="info-item">
                    <label>Date</label>
                    <value>{{ $event->start_date ? $event->start_date->format('d/m/Y à H:i') : 'À définir' }}</value>
                </div>
                <div class="info-item">
                    <label>Lieu</label>
                    <value>{{ $event->lieu ?? 'À définir' }} - {{ $event->ville ?? 'À définir' }}</value>
                </div>
                <div class="info-item">
                    <label>Référence</label>
                    <value>{{ $payment->matricule }}</value>
                </div>
                <div class="info-item">
                    <label>Type de billet</label>
                    <value>{{ $ticketType }}</value>
                </div>
                <div class="info-item">
                    <label>Prix</label>
                    <value>{{ $ticketPrice }}</value>
                </div>
            </div>
        </div>

        <!-- Billet en prévisualisation -->
        <div class="ticket-wrapper">
            @include('tickets.template', [
                'event' => $event,
                'payment' => $payment,
                'order' => $order,
                'ticket' => $ticket,
                'eventImageUrl' => $eventImageUrl,
                'qrCodeUrl' => $qrCodeUrl,
                'ticketType' => $ticketType,
                'ticketPrice' => $ticketPrice,
                'eventDate' => $eventDate,
                'sponsorLogos' => $sponsorLogos ?? [],
                'organizerLogoUrl' => $organizerLogoUrl ?? null,
                'fouleImageUrl' => $fouleImageUrl ?? null
            ])
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h4>📖 Comment utiliser cette prévisualisation</h4>
            <ul>
                <li>Cette page se recharge automatiquement quand vous modifiez le fichier <code>tickets/template.blade.php</code></li>
                <li>Utilisez le bouton "Actualiser" pour voir vos modifications</li>
                <li>Le bouton "Télécharger PDF" génère un vrai PDF avec vos modifications</li>
                <li>Les images, sponsors et logos sont chargés automatiquement depuis la base de données</li>
                <li>Pour utiliser un vrai paiement, ajoutez <code>?payment_id=XXX</code> dans l'URL</li>
            </ul>
        </div>
    </div>

    <script>
        // Auto-refresh toutes les 3 secondes (optionnel, commentez si vous ne voulez pas)
        // setInterval(() => location.reload(), 3000);
        
        // Raccourci clavier : Ctrl+R ou F5 pour actualiser
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey && e.key === 'r') || e.key === 'F5') {
                e.preventDefault();
                location.reload();
            }
        });
    </script>
</body>
</html>

