<?php
/**
 * Script de Migration des Données - MokiliEvent Microservices
 * 
 * Ce script migre les données de la base MySQL existante vers les nouvelles
 * bases PostgreSQL des microservices
 * 
 * Usage: php migrate-data.php [service]
 * Services: users, events, tickets, payments, notifications, all
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

class DataMigrator
{
    private $mysqlConnection;
    private $pgsqlConnections = [];
    
    public function __construct()
    {
        $this->setupConnections();
    }
    
    private function setupConnections()
    {
        // Configuration MySQL (source)
        DB::addConnection([
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'bd_mokili_v2',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ], 'mysql');
        
        // Configuration PostgreSQL (destinations)
        $services = [
            'user_service' => 'user_service',
            'event_service' => 'event_service',
            'ticket_service' => 'ticket_service',
            'payment_service' => 'payment_service',
            'notification_service' => 'notification_service',
            'analytics_service' => 'analytics_service'
        ];
        
        foreach ($services as $key => $database) {
            DB::addConnection([
                'driver' => 'pgsql',
                'host' => 'localhost',
                'database' => $database,
                'username' => 'mokili',
                'password' => 'secret',
                'charset' => 'utf8',
                'prefix' => '',
            ], $key);
        }
    }
    
    public function migrateUsers()
    {
        echo "🔄 Migration des utilisateurs...\n";
        
        $users = DB::connection('mysql')->table('users')->get();
        $count = 0;
        
        foreach ($users as $user) {
            try {
                DB::connection('user_service')->table('users')->insert([
                    'legacy_id' => $user->id,
                    'uuid' => $user->uuid ?? \Illuminate\Support\Str::uuid(),
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'genre' => $user->genre,
                    'tranche_age' => $user->tranche_age,
                    'email_verified_at' => $user->email_verified_at,
                    'password' => $user->password,
                    'two_factor_secret' => $user->two_factor_secret,
                    'two_factor_recovery_codes' => $user->two_factor_recovery_codes,
                    'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                    'phone' => $user->phone,
                    'profil_image' => $user->profil_image,
                    'is_profile_complete' => $user->is_profile_complete,
                    'address' => $user->address,
                    'city' => $user->city,
                    'country' => $user->country,
                    'is_active' => $user->is_active,
                    'remember_token' => $user->remember_token,
                    'otp' => $user->otp,
                    'otp_expires_at' => $user->otp_expires_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'deleted_at' => $user->deleted_at,
                ]);
                $count++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de l'utilisateur {$user->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$count} utilisateurs migrés avec succès\n";
    }
    
    public function migrateEvents()
    {
        echo "🔄 Migration des événements...\n";
        
        // Migration des catégories
        $categories = DB::connection('mysql')->table('categories')->get();
        $categoryMap = [];
        
        foreach ($categories as $category) {
            try {
                $newId = DB::connection('event_service')->table('categories')->insertGetId([
                    'legacy_id' => $category->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'deleted_at' => $category->deleted_at,
                ]);
                $categoryMap[$category->id] = $newId;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de la catégorie {$category->id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Migration des organisateurs
        $organizers = DB::connection('mysql')->table('organizers')->get();
        $organizerMap = [];
        
        foreach ($organizers as $organizer) {
            try {
                $newId = DB::connection('event_service')->table('organizers')->insertGetId([
                    'legacy_id' => $organizer->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'user_id' => $organizer->user_id, // Référence vers User Service
                    'company_name' => $organizer->company_name,
                    'email' => $organizer->email,
                    'phone_primary' => $organizer->phone_primary,
                    'address' => $organizer->address,
                    'logo_path' => $organizer->logo_path,
                    'description' => $organizer->description,
                    'website' => $organizer->website,
                    'social_media' => json_encode($organizer->social_media ?? []),
                    'is_verified' => $organizer->is_verified,
                    'created_at' => $organizer->created_at,
                    'updated_at' => $organizer->updated_at,
                ]);
                $organizerMap[$organizer->id] = $newId;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de l'organisateur {$organizer->id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Migration des événements
        $events = DB::connection('mysql')->table('events')->get();
        $count = 0;
        
        foreach ($events as $event) {
            try {
                DB::connection('event_service')->table('events')->insert([
                    'legacy_id' => $event->id,
                    'uuid' => $event->uuid ?? \Illuminate\Support\Str::uuid(),
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'description' => $event->description,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'lieu' => $event->lieu,
                    'adresse' => $event->adresse,
                    'ville' => $event->ville,
                    'pays' => $event->pays,
                    'adresse_map' => $event->adresse_map,
                    'image' => $event->image,
                    'is_featured' => $event->is_featured,
                    'is_approved' => $event->is_approved,
                    'status' => $event->status,
                    'category_id' => $categoryMap[$event->category_id] ?? null,
                    'organizer_id' => $organizerMap[$event->organizer_id] ?? null,
                    'etat' => $event->etat,
                    'publish_at' => $event->publish_at,
                    'is_published' => $event->is_published,
                    'event_type' => $event->event_type,
                    'keywords' => $event->keywords,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                    'deleted_at' => $event->deleted_at,
                ]);
                $count++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de l'événement {$event->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$count} événements migrés avec succès\n";
    }
    
    public function migrateTickets()
    {
        echo "🔄 Migration des billets...\n";
        
        // Migration des billets
        $tickets = DB::connection('mysql')->table('tickets')->get();
        $count = 0;
        
        foreach ($tickets as $ticket) {
            try {
                DB::connection('ticket_service')->table('tickets')->insert([
                    'legacy_id' => $ticket->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'event_id' => $ticket->event_id, // Référence vers Event Service
                    'name' => $ticket->name,
                    'description' => $ticket->description,
                    'price' => $ticket->price,
                    'quantity' => $ticket->quantity,
                    'sold_quantity' => $ticket->sold_quantity,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'deleted_at' => $ticket->deleted_at,
                ]);
                $count++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration du billet {$ticket->id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Migration des commandes
        $orders = DB::connection('mysql')->table('orders')->get();
        $orderCount = 0;
        
        foreach ($orders as $order) {
            try {
                DB::connection('ticket_service')->table('orders')->insert([
                    'legacy_id' => $order->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id, // Référence vers User Service
                    'event_id' => $order->event_id, // Référence vers Event Service
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
                $orderCount++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de la commande {$order->id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Migration des réservations
        $reservations = DB::connection('mysql')->table('reservations')->get();
        $reservationCount = 0;
        
        foreach ($reservations as $reservation) {
            try {
                DB::connection('ticket_service')->table('reservations')->insert([
                    'legacy_id' => $reservation->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'user_id' => $reservation->user_id, // Référence vers User Service
                    'event_id' => $reservation->event_id, // Référence vers Event Service
                    'status' => $reservation->status,
                    'expires_at' => $reservation->expires_at,
                    'seat_id' => $reservation->seat_id,
                    'created_at' => $reservation->created_at,
                    'updated_at' => $reservation->updated_at,
                ]);
                $reservationCount++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration de la réservation {$reservation->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$count} billets, {$orderCount} commandes et {$reservationCount} réservations migrés avec succès\n";
    }
    
    public function migratePayments()
    {
        echo "🔄 Migration des paiements...\n";
        
        $payments = DB::connection('mysql')->table('payments')->get();
        $count = 0;
        
        foreach ($payments as $payment) {
            try {
                DB::connection('payment_service')->table('payments')->insert([
                    'legacy_id' => $payment->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'payment_number' => $payment->payment_number,
                    'user_id' => $payment->user_id, // Référence vers User Service
                    'order_id' => $payment->order_id, // Référence vers Ticket Service
                    'amount' => $payment->amount,
                    'currency' => $payment->currency ?? 'XAF',
                    'status' => $payment->status,
                    'payment_method' => $payment->payment_method,
                    'phone_number' => $payment->phone_number,
                    'qr_code_path' => $payment->qr_code_path,
                    'transaction_id' => $payment->transaction_id,
                    'gateway_response' => json_encode($payment->gateway_response ?? []),
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'paid_at' => $payment->paid_at,
                    'failed_at' => $payment->failed_at,
                    'refunded_at' => $payment->refunded_at,
                ]);
                $count++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration du paiement {$payment->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$count} paiements migrés avec succès\n";
    }
    
    public function migrateNotifications()
    {
        echo "🔄 Migration des notifications...\n";
        
        // Migration des blogs
        $blogs = DB::connection('mysql')->table('blogs')->get();
        $count = 0;
        
        foreach ($blogs as $blog) {
            try {
                DB::connection('notification_service')->table('blogs')->insert([
                    'legacy_id' => $blog->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'content' => $blog->content,
                    'image' => $blog->image,
                    'user_id' => $blog->user_id, // Référence vers User Service
                    'category_id' => $blog->category_id,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                    'deleted_at' => $blog->deleted_at,
                ]);
                $count++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration du blog {$blog->id}: " . $e->getMessage() . "\n";
            }
        }
        
        // Migration des commentaires
        $comments = DB::connection('mysql')->table('comments')->get();
        $commentCount = 0;
        
        foreach ($comments as $comment) {
            try {
                DB::connection('notification_service')->table('comments')->insert([
                    'legacy_id' => $comment->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'user_id' => $comment->user_id, // Référence vers User Service
                    'commentable_type' => $comment->commentable_type,
                    'commentable_id' => $comment->commentable_id,
                    'content' => $comment->content,
                    'parent_id' => $comment->parent_id,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ]);
                $commentCount++;
            } catch (Exception $e) {
                echo "❌ Erreur lors de la migration du commentaire {$comment->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ {$count} blogs et {$commentCount} commentaires migrés avec succès\n";
    }
    
    public function migrateAll()
    {
        echo "🚀 Début de la migration complète...\n\n";
        
        $this->migrateUsers();
        echo "\n";
        
        $this->migrateEvents();
        echo "\n";
        
        $this->migrateTickets();
        echo "\n";
        
        $this->migratePayments();
        echo "\n";
        
        $this->migrateNotifications();
        echo "\n";
        
        echo "🎉 Migration complète terminée !\n";
    }
}

// Exécution du script
$service = $argv[1] ?? 'all';

$migrator = new DataMigrator();

switch ($service) {
    case 'users':
        $migrator->migrateUsers();
        break;
    case 'events':
        $migrator->migrateEvents();
        break;
    case 'tickets':
        $migrator->migrateTickets();
        break;
    case 'payments':
        $migrator->migratePayments();
        break;
    case 'notifications':
        $migrator->migrateNotifications();
        break;
    case 'all':
    default:
        $migrator->migrateAll();
        break;
}
