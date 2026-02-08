<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\Organizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UATSeeder extends Seeder
{
    /**
     * Run the database seeds for UAT environment.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting UAT data seeding...');

        // 1. Create test users
        $this->createUsers();

        // 2. Create categories
        $this->createCategories();

        // 3. Create events with tickets
        $this->createEvents();

        // 4. Create some reservations and orders
        $this->createReservationsAndOrders();

        $this->command->info('✅ UAT seeding completed successfully!');
    }

    private function createUsers(): void
    {
        $this->command->info('Creating test users...');

        // Admin user
        $admin = User::create([
            'prenom' => 'Admin',
            'nom' => 'UAT',
            'email' => 'admin@uat.test',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
            'phone' => '064000001',
            'genre' => 'Homme',
            'tranche_age' => '25-35',
            'is_profile_complete' => true,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Organizer user
        $organizer = User::create([
            'prenom' => 'Organisateur',
            'nom' => 'Test',
            'email' => 'orga@uat.test',
            'password' => Hash::make('Orga123!'),
            'email_verified_at' => now(),
            'phone' => '064000002',
            'genre' => 'Homme',
            'tranche_age' => '25-35',
            'is_profile_complete' => true,
            'is_active' => true,
        ]);
        $organizer->assignRole('organizer');

        // Create organizer profile
        Organizer::create([
            'user_id' => $organizer->id,
            'nom' => 'Events Test SARL',
            'description' => 'Organisateur de test pour l\'environnement UAT',
            'email' => 'orga@uat.test',
            'telephone' => '064000002',
            'statut' => 'approuvé',
        ]);

        // Standard user
        User::create([
            'prenom' => 'Utilisateur',
            'nom' => 'Test',
            'email' => 'user@uat.test',
            'password' => Hash::make('User123!'),
            'email_verified_at' => now(),
            'phone' => '064000003',
            'genre' => 'Femme',
            'tranche_age' => '18-25',
            'is_profile_complete' => true,
            'is_active' => true,
        ]);

        // Additional test users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'prenom' => "User{$i}",
                'nom' => 'Test',
                'email' => "user{$i}@uat.test",
                'password' => Hash::make('Test123!'),
                'email_verified_at' => now(),
                'phone' => '06400000' . (3 + $i),
                'genre' => $i % 2 === 0 ? 'Homme' : 'Femme',
                'tranche_age' => '18-25',
                'is_profile_complete' => true,
                'is_active' => true,
            ]);
        }

        $this->command->info('✓ Users created');
    }

    private function createCategories(): void
    {
        $this->command->info('Creating categories...');

        $categories = [
            ['nom' => 'Concert', 'description' => 'Concerts et spectacles musicaux'],
            ['nom' => 'Conférence', 'description' => 'Conférences et séminaires'],
            ['nom' => 'Sport', 'description' => 'Événements sportifs'],
            ['nom' => 'Gala', 'description' => 'Soirées de gala'],
            ['nom' => 'Festival', 'description' => 'Festivals culturels'],
            ['nom' => 'Formation', 'description' => 'Formations et ateliers'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✓ Categories created');
    }

    private function createEvents(): void
    {
        $this->command->info('Creating events...');

        $organizer = User::where('email', 'orga@uat.test')->first();
        $categories = Category::all();

        $events = [
            [
                'title' => 'Concert Jazz 2026',
                'description' => 'Grand concert de jazz avec des artistes internationaux. Soirée inoubliable au cœur de Brazzaville.',
                'date_debut' => now()->addDays(15),
                'heure_debut' => '19:00:00',
                'lieu' => 'Salle de Spectacle, Brazzaville',
                'category_id' => $categories->where('nom', 'Concert')->first()->id,
                'tickets' => [
                    ['nom' => 'Standard', 'prix' => 7596, 'quantite' => 100],
                    ['nom' => 'VIP', 'prix' => 15000, 'quantite' => 30],
                ],
            ],
            [
                'title' => 'Conférence Tech Innovation',
                'description' => 'Conférence sur l\'innovation technologique en Afrique. Intervenants de renommée internationale.',
                'date_debut' => now()->addDays(30),
                'heure_debut' => '09:00:00',
                'lieu' => 'Radisson Blu, Pointe-Noire',
                'category_id' => $categories->where('nom', 'Conférence')->first()->id,
                'tickets' => [
                    ['nom' => 'Pass 1 jour', 'prix' => 12000, 'quantite' => 150],
                    ['nom' => 'Pass 3 jours', 'prix' => 30000, 'quantite' => 50],
                ],
            ],
            [
                'title' => 'Gala de Charité',
                'description' => 'Gala de charité au profit des enfants défavorisés. Dîner et spectacle.',
                'date_debut' => now()->addDays(45),
                'heure_debut' => '20:00:00',
                'lieu' => 'Hôtel Kempinski, Brazzaville',
                'category_id' => $categories->where('nom', 'Gala')->first()->id,
                'tickets' => [
                    ['nom' => 'Table (10 pers)', 'prix' => 500000, 'quantite' => 20],
                    ['nom' => 'Billet Individuel', 'prix' => 50000, 'quantite' => 100],
                ],
            ],
            [
                'title' => 'Festival de Musique Urbaine',
                'description' => '3 jours de concerts avec les meilleurs artistes du rap et hip-hop congolais.',
                'date_debut' => now()->addDays(60),
                'heure_debut' => '18:00:00',
                'lieu' => 'Plage Mondongo, Pointe-Noire',
                'category_id' => $categories->where('nom', 'Festival')->first()->id,
                'tickets' => [
                    ['nom' => 'Pass 3 jours', 'prix' => 25000, 'quantite' => 500],
                    ['nom' => 'Pass VIP 3 jours', 'prix' => 60000, 'quantite' => 100],
                    ['nom' => 'Billet Jour 1', 'prix' => 10000, 'quantite' => 200],
                ],
            ],
            [
                'title' => 'Formation Marketing Digital',
                'description' => 'Atelier pratique de 2 jours sur le marketing digital et les réseaux sociaux.',
                'date_debut' => now()->addDays(20),
                'heure_debut' => '08:30:00',
                'lieu' => 'Centre de Formation CGECI, Brazzaville',
                'category_id' => $categories->where('nom', 'Formation')->first()->id,
                'tickets' => [
                    ['nom' => 'Inscription Standard', 'prix' => 35000, 'quantite' => 40],
                    ['nom' => 'Inscription Premium', 'prix' => 50000, 'quantite' => 10],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $ticketsData = $eventData['tickets'];
            unset($eventData['tickets']);

            $event = Event::create([
                ...$eventData,
                'organisateur_id' => $organizer->id,
                'status' => 'active',
                'type' => 'public',
                'date_fin' => $eventData['date_debut']->copy()->addHours(5),
            ]);

            // Create tickets for this event
            foreach ($ticketsData as $ticketData) {
                Ticket::create([
                    'event_id' => $event->id,
                    'nom' => $ticketData['nom'],
                    'prix' => $ticketData['prix'],
                    'quantite' => $ticketData['quantite'],
                    'quantite_vendue' => 0,
                    'statut' => 'disponible',
                ]);
            }
        }

        $this->command->info('✓ Events and tickets created');
    }

    private function createReservationsAndOrders(): void
    {
        $this->command->info('Creating test reservations and orders...');

        $user = User::where('email', 'user@uat.test')->first();
        $events = Event::with('tickets')->get();

        // Create a confirmed reservation with order
        $firstEvent = $events->first();
        $firstTicket = $firstEvent->tickets->first();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'ticket_id' => $firstTicket->id,
            'quantity' => 2,
            'status' => 'Confirmé',
            'expires_at' => now()->addDays(10),
        ]);

        // Create corresponding order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'total_amount' => $firstTicket->prix * 2,
            'status' => 'completed',
        ]);

        // Create a pending reservation (not paid)
        $secondEvent = $events->skip(1)->first();
        $secondTicket = $secondEvent->tickets->first();

        Reservation::create([
            'user_id' => $user->id,
            'ticket_id' => $secondTicket->id,
            'quantity' => 1,
            'status' => 'Réservé',
            'expires_at' => now()->addMinutes(30),
        ]);

        $this->command->info('✓ Reservations and orders created');
    }
}
