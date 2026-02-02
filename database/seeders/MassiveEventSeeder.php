<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MassiveEventSeeder extends Seeder
{
    /**
     * Télécharger une image d'événement aléatoire
     */
    private function downloadEventImage(int $index): string
    {
        // Utiliser plusieurs sources d'images
        $imageUrls = [
            "https://source.unsplash.com/random/800x600/?event,conference",
            "https://source.unsplash.com/random/800x600/?festival,music",
            "https://source.unsplash.com/random/800x600/?concert,stage",
            "https://source.unsplash.com/random/800x600/?sports,stadium",
            "https://source.unsplash.com/random/800x600/?art,gallery",
        ];
        
        $imageUrl = $imageUrls[$index % count($imageUrls)];
        $filename = "events/event-{$index}.jpg";
        
        try {
            $response = Http::timeout(15)->get($imageUrl);
            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            $this->command->warn("⚠️ Impossible de télécharger l'image pour l'événement {$index}");
        }
        
        return "events/default-event.jpg";
    }
    
    /**
     * Télécharger une image d'organisateur aléatoire
     */
    private function downloadOrganizerImage(int $index): string
    {
        $imageUrl = "https://source.unsplash.com/random/400x400/?company,business,logo";
        $filename = "organizers/organizer-{$index}.jpg";
        
        try {
            $response = Http::timeout(15)->get($imageUrl);
            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            $this->command->warn("⚠️ Impossible de télécharger l'image pour l'organisateur {$index}");
        }
        
        return "organizers/default-organizer.jpg";
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les dossiers s'ils n'existent pas
        if (!Storage::exists('public/events')) {
            Storage::makeDirectory('public/events');
        }
        if (!Storage::exists('public/organizers')) {
            Storage::makeDirectory('public/organizers');
        }
        // Récupérer les catégories existantes
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('Aucune catégorie trouvée. Veuillez d\'abord créer les catégories.');
            return;
        }

        $this->command->info('🚀 Création de 30 organisateurs...');

        // Créer 30 organisateurs
        $organizers = [];
        $timestamp = now()->timestamp;
        
        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'prenom' => fake('fr_FR')->firstName(),
                'nom' => fake('fr_FR')->lastName(),
                'email' => 'organizer' . $timestamp . '_' . $i . '@mokilievent.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'phone' => fake()->phoneNumber(),
                'city' => fake()->city(),
                'country' => 'Congo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assigner le rôle Organizer
            $user->assignRole('Organizer');

            $companyName = fake('fr_FR')->company();
            $logoPath = $this->downloadOrganizerImage($i);
            
            $organizer = Organizer::create([
                'user_id' => $user->id,
                'company_name' => $companyName,
                'slug' => Str::slug($companyName) . '-' . $timestamp . '-' . $i,
                'slogan' => fake('fr_FR')->sentence(),
                'description' => fake('fr_FR')->paragraph(3),
                'logo' => $logoPath,
                'email' => $user->email,
                'phone_primary' => fake()->phoneNumber(),
                'website' => fake()->url(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'country' => 'Congo',
                'is_verified' => fake()->boolean(80),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $organizers[] = $organizer;
            $this->command->line("✅ Organisateur {$i}/30 créé : {$organizer->company_name}");
        }

        $this->command->info('📅 Création de 100+ événements avec images...');

        // Images d'événements disponibles (vous pouvez utiliser des URLs d'images réelles)
        $eventImages = [
            'events/concert-1.jpg',
            'events/concert-2.jpg',
            'events/art-1.jpg',
            'events/sport-1.jpg',
            'events/conference-1.jpg',
            'events/festival-1.jpg',
            'events/famille-1.jpg',
            'events/mode-1.jpg',
            'events/tech-1.jpg',
            'events/culture-1.jpg',
        ];

        $villes = ['Brazzaville', 'Pointe-Noire', 'Dolisie', 'Nkayi', 'Ouesso', 'Impfondo', 'Sibiti', 'Loango'];
        $eventTypes = ['Espace libre', 'Plan de salle', 'Mixte'];
        $etats = ['En cours', 'En attente', 'Archivé'];
        $statusTypes = ['Payant', 'Gratuit'];

        $eventTitles = [
            // Concerts & Festivals
            'Festival de Musique Africaine', 'Concert Jazz en Plein Air', 'Nuit Électro',
            'Concerto Classique', 'Reggae Vibes Festival', 'Hip-Hop Summit',
            
            // Art & Culture
            'Exposition Art Contemporain', 'Théâtre en Plein Air', 'Danse Traditionnelle',
            'Galerie Photo Moderne', 'Atelier Poterie', 'Spectacle Cirque',
            
            // Sport
            'Marathon Urbain', 'Tournoi de Football', 'Tournoi de Basketball',
            'Course Cycliste', 'Match de Volley-Ball', 'Tournoi Tennis',
            
            // Éducation & Conférence
            'Conférence Tech Startups', 'Séminaire Leadership', 'Forum Innovation',
            'Atelier Développement Web', 'Formation IA', 'Salon Étudiant',
            
            // Famille & Santé
            'Journée Santé Bien-être', 'Atelier Nutrition', 'Marche Familiale',
            'Festival Enfant', 'Forum Parents', 'Expo Bébé',
            
            // Mode & Lifestyle
            'Défilé de Mode', 'Showroom Automobile', 'Salon Beauté',
            'Fashion Week', 'Exposition Luxe', 'Design & Innovation',
        ];

        $descriptions = [
            'Découvrez un événement exceptionnel qui rassemble passionnés et professionnels. Une expérience unique à ne pas manquer!',
            'Rejoignez-nous pour célébrer la culture et la créativité. Moments inoubliables garantis dans une atmosphère conviviale.',
            'Un rendez-vous incontournable pour tous les amateurs. Participez à cette expérience enrichissante et enrichissante.',
            'Vivez une aventure culturelle exceptionnelle. Des activités variées vous attendent pour tous les âges.',
            'Événement phare de l\'année, une occasion unique de réseauter et de découvrir de nouvelles perspectives.',
        ];

        $eventsCreated = 0;
        $eventsToCreate = 120; // Plus de 100

        foreach ($organizers as $organizerIndex => $organizer) {
            // Chaque organisateur a entre 3 et 8 événements
            $eventsPerOrganizer = rand(3, 8);
            
            for ($j = 0; $j < $eventsPerOrganizer && $eventsCreated < $eventsToCreate; $j++) {
                $startDate = Carbon::now()->addDays(rand(1, 180)); // Événements dans les 6 prochains mois
                $endDate = $startDate->copy()->addHours(rand(2, 48));
                
                $ville = fake()->randomElement($villes);
                $isFeatured = fake()->boolean(20); // 20% des événements en vedette
                
                $eventTitle = fake()->randomElement($eventTitles) . ' ' . rand(2025, 2026);
                
                $eventSlug = Str::slug($eventTitle) . '-' . uniqid() . '-' . $eventsCreated;
                
                // Télécharger une image pour l'événement
                $eventImagePath = $this->downloadEventImage($eventsCreated);
                
                // Créer l'événement sans l'observer pour éviter le conflit de slug
                $event = Event::withoutEvents(function() use ($eventTitle, $eventSlug, $descriptions, $startDate, $endDate, $ville, $eventImagePath, $isFeatured, $categories, $organizer, $etats, $eventTypes, $statusTypes) {
                    return Event::create([
                        'title' => $eventTitle,
                        'slug' => $eventSlug,
                        'description' => fake()->randomElement($descriptions) . ' ' . fake('fr_FR')->paragraph(),
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'lieu' => fake()->randomElement(['Centre', 'Stade', 'Salle', 'Parc', 'Place', 'Complexe']) . ' ' . fake()->firstName(),
                        'adresse' => fake()->streetAddress(),
                        'ville' => $ville,
                        'pays' => 'Congo',
                        'adresse_map' => '<iframe src="https://www.google.com/maps?q=' . urlencode($ville . ', Congo') . '&output=embed" width="100%" height="400" frameborder="0" style="border:0"></iframe>',
                        'image' => $eventImagePath,
                        'is_featured' => $isFeatured,
                        'is_approved' => true,
                        'is_published' => true,
                        'status' => fake()->randomElement($statusTypes),
                        'category_id' => $categories->random()->id,
                        'organizer_id' => $organizer->id,
                        'etat' => fake()->randomElement($etats),
                        'publish_at' => now()->subDays(rand(1, 30)),
                        'event_type' => fake()->randomElement($eventTypes),
                        'keywords' => json_encode(fake()->words(5)),
                        'created_at' => now()->subDays(rand(1, 60)),
                        'updated_at' => now()->subDays(rand(1, 60)),
                    ]);
                });
                
                // Vérifier que l'événement a bien été créé
                if (!$event) {
                    $this->command->error("❌ Échec de création de l'événement {$eventsCreated}");
                    continue;
                }

                // Créer 2-5 tickets pour chaque événement
                $numTickets = rand(2, 5);
                for ($t = 0; $t < $numTickets; $t++) {
                    Ticket::create([
                        'nom' => fake()->randomElement(['VIP', 'Standard', 'Early Bird', 'Étudiant', 'Groupe']) . ' ' . ($t + 1),
                        'description' => fake()->sentence(),
                        'prix' => $event->status === 'Gratuit' ? 0 : fake()->numberBetween(5000, 50000),
                        'quantite' => fake()->numberBetween(50, 500),
                        'quantite_vendue' => fake()->numberBetween(0, 200),
                        'statut' => fake()->randomElement(['disponible', 'épuisé']),
                        'event_id' => $event->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $eventsCreated++;
                $this->command->line("✅ Événement {$eventsCreated}/{$eventsToCreate} créé : {$event->title}");
            }
        }

        $this->command->info("🎉 Terminé ! {$eventsCreated} événements créés pour 30 organisateurs !");
    }
}
