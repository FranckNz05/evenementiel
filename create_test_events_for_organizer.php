<?php

/**
 * Créer des événements de test avec paiements pour l'organisateur organizer1@mokilievent.com
 */

echo "🎪 CRÉATION ÉVÉNEMENTS DE TEST POUR ORGANIZER1\n";
echo str_repeat("=", 60) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

echo "1️⃣ RECHERCHE ORGANISATEUR :\n\n";

// Trouver l'utilisateur et son organizer
$user = DB::table('users')->where('email', 'organizer1@mokilievent.com')->first();
if (!$user) {
    echo "❌ Utilisateur organizer1@mokilievent.com non trouvé\n\n";
    exit;
}

$organizer = DB::table('organizers')->where('user_id', $user->id)->first();
if (!$organizer) {
    echo "❌ Profil organizer non trouvé pour cet utilisateur\n\n";
    exit;
}

echo "✅ Organisateur trouvé :\n";
echo "   - User ID : {$user->id}\n";
echo "   - Organizer ID : {$organizer->id}\n";
echo "   - Company : {$organizer->company_name}\n\n";

echo "2️⃣ CRÉATION D'ÉVÉNEMENTS DE TEST :\n\n";

// Créer 3 événements de test
$eventsData = [
    [
        'title' => 'Concert Jazz Brazzaville 2026',
        'description' => 'Un concert exceptionnel de jazz avec les meilleurs artistes congolais et africains.',
        'date' => Carbon::now()->addDays(30)->format('Y-m-d H:i:s'),
        'location' => 'Palais des Congrès, Brazzaville',
        'capacity' => 500,
        'price' => 15000,
        'category' => 'Musique',
        'status' => 'published'
    ],
    [
        'title' => 'Festival de Théâtre Africain',
        'description' => 'Découvrez les talents théâtraux d\'Afrique avec des pièces contemporaines et traditionnelles.',
        'date' => Carbon::now()->addDays(45)->format('Y-m-d H:i:s'),
        'location' => 'Théâtre National, Brazzaville',
        'capacity' => 300,
        'price' => 10000,
        'category' => 'Théâtre',
        'status' => 'published'
    ],
    [
        'title' => 'Exposition Art Contemporain',
        'description' => 'Découvrez les œuvres des artistes contemporains congolais et africains.',
        'date' => Carbon::now()->addDays(60)->format('Y-m-d H:i:s'),
        'location' => 'Musée des Beaux-Arts, Brazzaville',
        'capacity' => 200,
        'price' => 8000,
        'category' => 'Art',
        'status' => 'published'
    ]
];

$createdEvents = [];
foreach ($eventsData as $index => $eventData) {
    try {
        // Créer l'événement
        $eventId = DB::table('events')->insertGetId([
            'organizer_id' => $organizer->id,
            'title' => $eventData['title'],
            'slug' => Str::slug($eventData['title']) . '-' . time() . '-' . $index,
            'description' => $eventData['description'],
            'start_date' => $eventData['date'],
            'end_date' => Carbon::parse($eventData['date'])->addHours(3)->format('Y-m-d H:i:s'), // 3h de durée
            'lieu' => $eventData['location'],
            'adresse' => $eventData['location'],
            'ville' => 'Brazzaville',
            'pays' => 'Congo',
            'category_id' => 1, // Première catégorie disponible
            'status' => 'Payant',
            'etat' => 'En cours',
            'is_published' => 1,
            'is_approved' => 1,
            'image' => 'events/default-event.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Événement créé : {$eventData['title']} (ID: {$eventId})\n";

        $createdEvents[] = [
            'id' => $eventId,
            'title' => $eventData['title'],
            'price' => $eventData['price'],
            'capacity' => $eventData['capacity']
        ];

    } catch (\Exception $e) {
        echo "❌ Erreur création événement {$eventData['title']}: {$e->getMessage()}\n";
    }
}

echo "\n3️⃣ CRÉATION DE PAIEMENTS SIMULÉS :\n\n";

// Pour chaque événement, créer des tickets et paiements
$totalRevenue = 0;
foreach ($createdEvents as $event) {
    $ticketsSold = rand(50, min(100, $event['capacity'])); // Vendre 50-100 tickets
    $eventRevenue = 0;

    echo "🎫 Traitement événement : {$event['title']}\n";
    echo "   - Prix ticket : " . number_format($event['price'], 0, ',', ' ') . " FCFA\n";
    echo "   - Tickets à vendre : {$ticketsSold}\n\n";

    // Créer des tickets vendus
    for ($i = 0; $i < $ticketsSold; $i++) {
        try {
            // Créer un ticket
            $ticketId = DB::table('tickets')->insertGetId([
                'event_id' => $event['id'],
                'type' => 'Standard',
                'price' => $event['price'],
                'quantity_available' => 1,
                'quantity_sold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Créer un paiement réussi
            $paymentId = DB::table('paiements')->insertGetId([
                'matricule' => 'PAY-' . strtoupper(Str::random(8)),
                'user_id' => rand(1, 50), // Utilisateur aléatoire
                'order_id' => null,
                'reservation_id' => null,
                'evenement_id' => $event['id'],
                'order_ticket_id' => null,
                'montant' => $event['price'],
                'statut' => 'payé',
                'methode_paiement' => 'Airtel Money',
                'date_paiement' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                'reference_paiement' => 'REF-' . strtoupper(Str::random(10)),
                'numero_telephone' => '+24206' . rand(1000000, 9999999),
                'details' => json_encode([
                    'event_title' => $event['title'],
                    'ticket_type' => 'Standard',
                    'payment_method' => 'Airtel Money',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12))
                ]),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $eventRevenue += $event['price'];

        } catch (\Exception $e) {
            echo "❌ Erreur paiement {$i} pour {$event['title']}: {$e->getMessage()}\n";
        }
    }

    echo "   - Revenus générés : " . number_format($eventRevenue, 0, ',', ' ') . " FCFA\n\n";
    $totalRevenue += $eventRevenue;
}

echo "4️⃣ RÉCAPITULATIF :\n\n";
echo "✅ Événements créés : " . count($createdEvents) . "\n";
echo "💰 Revenus totaux générés : " . number_format($totalRevenue, 0, ',', ' ') . " FCFA\n\n";

// Vérifier les retraits existants
$existingWithdrawals = DB::table('withdrawals')->where('organizer_id', $organizer->id)->sum('amount');
$soldeDisponible = $totalRevenue - $existingWithdrawals;

echo "💸 SOLDE DISPONIBLE :\n";
echo "   - Revenus bruts : " . number_format($totalRevenue, 0, ',', ' ') . " FCFA\n";
echo "   - Retraits déjà effectués : " . number_format($existingWithdrawals, 0, ',', ' ') . " FCFA\n";
echo "   - Solde disponible : " . number_format($soldeDisponible, 0, ',', ' ') . " FCFA\n\n";

if ($soldeDisponible >= 1000) {
    echo "✅ SOLDE SUFFISANT pour tester les retraits !\n\n";
} else {
    echo "❌ Solde insuffisant\n\n";
}

echo "🎯 PROCHAINES ÉTAPES :\n\n";
echo "1. Rafraîchissez votre tableau de bord organizer\n";
echo "2. Vérifiez que les événements apparaissent\n";
echo "3. Allez dans Paiements → Retraits\n";
echo "4. Testez une demande de retrait avec Airtel Money\n\n";

echo str_repeat("=", 60) . "\n";
echo "🎪 ÉVÉNEMENTS DE TEST CRÉÉS AVEC SUCCÈS !\n";
echo str_repeat("=", 60) . "\n";
