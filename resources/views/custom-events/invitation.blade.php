<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation - {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .invitation-container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .invitation-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5490 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .invitation-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .invitation-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }
        .invitation-body {
            padding: 40px 30px;
        }
        .event-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .event-detail {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 12px;
        }
        .event-detail i {
            font-size: 24px;
            color: #0f3460;
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }
        .event-detail-content {
            flex: 1;
        }
        .event-detail-label {
            font-size: 0.85rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .event-detail-value {
            font-size: 1.1rem;
            color: #1a202c;
            font-weight: 600;
        }
        .event-description {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            color: #4a5568;
            line-height: 1.6;
        }
        .organizer-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }
        .organizer-info p {
            color: #718096;
            margin: 5px 0;
        }
        @media (max-width: 768px) {
            .invitation-header h1 {
                font-size: 1.5rem;
            }
            .invitation-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invitation-container">
        <div class="invitation-header">
            <h1><i class="fas fa-calendar-check me-2"></i>Vous êtes invité !</h1>
            <p>{{ $event->title }}</p>
        </div>
        
        <div class="invitation-body">
            @if($event->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($event->image) }}" alt="{{ $event->title }}" class="event-image">
            @endif
            
            @if($event->start_date)
            <div class="event-detail">
                <i class="fas fa-calendar-alt"></i>
                <div class="event-detail-content">
                    <div class="event-detail-label">Date</div>
                    <div class="event-detail-value">
                        {{ $event->start_date->format('d/m/Y à H:i') }}
                        @if($event->end_date)
                            - {{ $event->end_date->format('d/m/Y à H:i') }}
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="event-detail">
                <i class="fas fa-map-marker-alt"></i>
                <div class="event-detail-content">
                    <div class="event-detail-label">Lieu</div>
                    <div class="event-detail-value">{{ $event->location }}</div>
                </div>
            </div>
            
            @if($event->type)
            <div class="event-detail">
                <i class="fas fa-tag"></i>
                <div class="event-detail-content">
                    <div class="event-detail-label">Type d'événement</div>
                    <div class="event-detail-value">{{ ucfirst($event->type) }}</div>
                </div>
            </div>
            @endif
            
            @if($event->description)
            <div class="event-description">
                <strong><i class="fas fa-info-circle me-2"></i>Description :</strong>
                <p class="mt-2 mb-0">{{ $event->description }}</p>
            </div>
            @endif
            
            <div class="organizer-info">
                <p><i class="fas fa-user me-2"></i>Organisé par {{ $event->organizer->prenom }} {{ $event->organizer->nom }}</p>
                @if($event->organizer->email)
                <p><i class="fas fa-envelope me-2"></i>{{ $event->organizer->email }}</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

