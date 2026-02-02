<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de Réservation</title>
</head>
<body>
    <h1>Merci pour votre réservation !</h1>
    <p>Réservation #{{ $reservation->id }}</p>
    <p>Événement : {{ $reservation->event->title }}</p>
    <p>Date : {{ $reservation->event->start_date->format('d/m/Y H:i') }}</p>
    <!-- Ajoutez plus de détails selon vos besoins -->
</body>
</html>
