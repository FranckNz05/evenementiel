<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use App\Models\Blog;
use App\Models\NewsletterSubscriber;
use App\Models\EventPublicationRequest;
use App\Models\OrganizerRequest;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Withdrawal;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\SettingsHelper;
use App\Models\CustomOfferPurchase;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:Administrateur');
    }

    public function index()
    {
        try {
            // Calcul des pourcentages de paiement par mode (basé sur les vraies données de la BD)
            // Utiliser la constante du modèle pour plus de sécurité
            $totalPayments = Payment::where('statut', Payment::STATUS_PAID)->count() ?: 1; // Éviter division par zéro
            $mtnPayments = Payment::where('statut', Payment::STATUS_PAID)
                ->where(function($query) {
                    $query->where('methode_paiement', 'MTN Mobile Money')
                          ->orWhere('methode_paiement', 'LIKE', '%MTN%')
                          ->orWhere('methode_paiement', 'LIKE', '%mtn%');
                })->count();
            $airtelPayments = Payment::where('statut', Payment::STATUS_PAID)
                ->where(function($query) {
                    $query->where('methode_paiement', 'Airtel Money')
                          ->orWhere('methode_paiement', 'LIKE', '%Airtel%')
                          ->orWhere('methode_paiement', 'LIKE', '%airtel%');
                })->count();
            $mobileMoneyPayments = Payment::where('statut', Payment::STATUS_PAID)
                ->where('methode_paiement', 'Mobile Money')
                ->where('methode_paiement', 'NOT LIKE', '%MTN%')
                ->where('methode_paiement', 'NOT LIKE', '%Airtel%')
                ->count();

            $mtnPercentage = round(($mtnPayments / $totalPayments) * 100);
            $airtelPercentage = round(($airtelPayments / $totalPayments) * 100);
            $mobileMoneyPercentage = round(($mobileMoneyPayments / $totalPayments) * 100);

            // Revenus événements classiques
            $eventsRevenue = Payment::where('statut', Payment::STATUS_PAID)->sum('montant');

            // Revenus événements personnalisés (achats d'offres)
            $customRevenue = CustomOfferPurchase::sum('price');
            $customPurchasesCount = CustomOfferPurchase::count();
            $topCustomPlan = CustomOfferPurchase::select('plan', DB::raw('COUNT(*) as count'))
                ->groupBy('plan')
                ->orderByDesc('count')
                ->first();

            // Bénéfices estimés (ex: 10% commissions sur ventes classiques uniquement)
            $benefits = round($eventsRevenue * 0.10, 2);

            // Statistiques générales (basées sur les vraies données de la BD)
            $stats = [
                'users_count' => User::count(),
                'events_count' => Event::count(),
                'total_tickets_sold' => Payment::where('statut', Payment::STATUS_PAID)->count(), // Nombre de paiements = tickets vendus
                'revenue' => ($eventsRevenue + $customRevenue) ?: 0,
                'revenue_breakdown' => [
                    'events' => (float) $eventsRevenue,
                    'custom_offers' => (float) $customRevenue,
                ],
                'custom_offers_count' => (int) $customPurchasesCount,
                'custom_offers_top_plan' => $topCustomPlan ? [
                    'plan' => $topCustomPlan->plan,
                    'count' => (int) $topCustomPlan->count,
                ] : null,
                'pending_events' => EventPublicationRequest::where('status', 'pending')->count() ?: 0,
                'pending_organizers' => OrganizerRequest::where('status', 'pending')->count() ?: 0,
                'newsletter_subscribers' => NewsletterSubscriber::where('is_active', true)->count() ?: 0,
                'blogs_count' => Blog::count() ?: 0,
                'mtn_payments_percentage' => $mtnPercentage,
                'airtel_payments_percentage' => $airtelPercentage,
                'mobile_money_payments_percentage' => $mobileMoneyPercentage,
                'benefits' => $benefits,
                'total_withdrawals' => Withdrawal::where('status', 'completed')->sum('amount') ?: 0,
                'withdrawals_count' => Withdrawal::count() ?: 0,
            ];

            // Événements et blogs populaires
            $popularEvents = Event::with('category')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $popularBlogs = Blog::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Données pour les graphiques
            $ticketSalesData = $this->getTicketSalesData();
            $categoryDistributionData = $this->getCategoryDistributionData();
            $recentActivities = $this->getRecentActivities();

            return view('dashboard.admin.dashboard', compact(
                'stats',
                'popularEvents',
                'popularBlogs',
                'ticketSalesData',
                'categoryDistributionData',
                'recentActivities'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du tableau de bord administrateur', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback: afficher un dashboard minimal au lieu de rediriger vers l'accueil
            $stats = [
                'totalUsers' => 0,
                'totalEvents' => 0,
                'totalPayments' => 0,
                'pendingEvents' => 0,
            ];
            $popularEvents = collect();
            $popularBlogs = collect();
            $ticketSalesData = ['labels' => [], 'data' => []];
            $categoryDistributionData = ['labels' => [], 'data' => []];
            $recentActivities = collect();

            return view('dashboard.admin.dashboard', compact(
                'stats',
                'popularEvents',
                'popularBlogs',
                'ticketSalesData',
                'categoryDistributionData',
                'recentActivities'
            ))->with('error', 'Certaines données n\'ont pas pu être chargées.');
        }
    }

    protected function getTicketSalesData()
    {
        $days = 7; // Derniers 7 jours
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            // Utiliser les vraies données de paiements
            $data[] = Payment::where('statut', Payment::STATUS_PAID)
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getCategoryDistributionData()
    {
        $categories = DB::table('categories')
            ->leftJoin('events', 'categories.id', '=', 'events.category_id')
            ->select('categories.name', DB::raw('count(events.id) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return [
            'labels' => $categories->pluck('name'),
            'data' => $categories->pluck('count')
        ];
    }

    protected function getRecentActivities()
    {
        $activities = [];

        // 1. Nouvelles inscriptions d'utilisateurs
        $recentUsers = User::latest()
            ->take(10)
            ->get();

        foreach ($recentUsers as $user) {
            $activities[] = [
                'created_at' => $user->created_at,
                'user_name' => ($user->prenom ?? '') . ' ' . ($user->nom ?? ''),
                'description' => '🎉 Nouvel utilisateur inscrit',
                'properties' => [
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'Non renseigné'
                ],
                'type' => 'user_registration'
            ];
        }

        // 2. Demandes pour devenir organisateur
        $organizerRequests = OrganizerRequest::with('user:id,prenom,nom,email')
            ->latest()
            ->take(10)
            ->get();

        foreach ($organizerRequests as $request) {
            $userName = $request->user 
                ? ($request->user->prenom ?? '') . ' ' . ($request->user->nom ?? '')
                : 'Utilisateur inconnu';

            // Supporter les statuts en français et en anglais
            $statusEmoji = [
                'pending' => '⏳',
                'en attente' => '⏳',
                'approved' => '✅',
                'approuvé' => '✅',
                'approuvée' => '✅',
                'rejected' => '❌',
                'rejeté' => '❌',
                'rejetée' => '❌'
            ];

            $statusText = [
                'pending' => 'en attente',
                'en attente' => 'en attente',
                'approved' => 'approuvée',
                'approuvé' => 'approuvée',
                'approuvée' => 'approuvée',
                'rejected' => 'rejetée',
                'rejeté' => 'rejetée',
                'rejetée' => 'rejetée'
            ];

            $emoji = $statusEmoji[$request->status] ?? '📋';
            $text = $statusText[$request->status] ?? $request->status;

            $activities[] = [
                'created_at' => $request->created_at,
                'user_name' => $userName,
                'description' => $emoji . ' Demande organisateur ' . $text,
                'properties' => [
                    'company' => $request->company_name,
                    'status' => ucfirst($text),
                    'email' => $request->email
                ],
                'type' => 'organizer_request'
            ];
        }

        // 3. Derniers paiements effectués
        $recentPayments = Payment::with([
            'user:id,prenom,nom',
            'event:id,title'
        ])
        ->where('statut', Payment::STATUS_PAID)
        ->latest()
        ->take(10)
        ->get();

        foreach ($recentPayments as $payment) {
            $userName = $payment->user 
                ? ($payment->user->prenom ?? '') . ' ' . ($payment->user->nom ?? '')
                : 'Utilisateur inconnu';

            $activities[] = [
                'created_at' => $payment->created_at,
                'user_name' => $userName,
                'description' => '💳 Paiement effectué',
                'properties' => [
                    'event' => $payment->event->title ?? 'Événement inconnu',
                    'amount' => number_format($payment->montant, 0, ',', ' ') . ' FCFA',
                    'method' => $payment->methode_paiement ?? 'Non spécifié'
                ],
                'type' => 'payment'
            ];
        }

        // 4. Derniers événements créés
        $recentEvents = Event::with([
                'organizer:id,company_name',
                'category:id,name'
            ])
            ->latest()
            ->take(10)
            ->get();

        foreach ($recentEvents as $event) {
            $startDate = $event->start_date;
            if (is_string($startDate)) {
                try {
                    $startDate = \Carbon\Carbon::parse($startDate);
                } catch (\Exception $e) {
                    $startDate = null;
                }
            }

            $formattedDate = $startDate ? $startDate->format('d/m/Y') : 'Date non disponible';

            $activities[] = [
                'created_at' => $event->created_at,
                'user_name' => $event->organizer->company_name ?? 'Organisateur inconnu',
                'description' => '📅 Nouvel événement créé',
                'properties' => [
                    'title' => $event->title,
                    'date' => $formattedDate,
                    'category' => $event->category->name ?? 'Non catégorisé'
                ],
                'type' => 'event'
            ];
        }

        // Tri par date décroissante et limitation à 15 éléments
        usort($activities, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return array_slice($activities, 0, 15);
    }

    /**
     * Affiche toutes les activités avec pagination
     */
    public function activities(Request $request)
    {
        $perPage = 20;
        $allActivities = $this->getAllActivities();
        
        // Tri par date décroissante
        usort($allActivities, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });
        
        // Convertir en collection pour la pagination
        $activitiesCollection = collect($allActivities);
        $total = $activitiesCollection->count();
        $currentPage = $request->get('page', 1);
        
        // Pagination manuelle
        $paginatedActivities = $activitiesCollection
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedActivities,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('dashboard.admin.activities', [
            'activities' => $paginator
        ]);
    }

    /**
     * Récupère toutes les activités (sans limite)
     */
    protected function getAllActivities()
    {
        $activities = [];

        // 1. Nouvelles inscriptions d'utilisateurs
        $recentUsers = User::latest()->get();

        foreach ($recentUsers as $user) {
            $activities[] = [
                'created_at' => $user->created_at,
                'user_name' => ($user->prenom ?? '') . ' ' . ($user->nom ?? ''),
                'description' => '🎉 Nouvel utilisateur inscrit',
                'properties' => [
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'Non renseigné'
                ],
                'type' => 'user_registration'
            ];
        }

        // 2. Demandes pour devenir organisateur
        $organizerRequests = OrganizerRequest::with('user:id,prenom,nom,email')
            ->latest()
            ->get();

        foreach ($organizerRequests as $request) {
            $userName = $request->user 
                ? ($request->user->prenom ?? '') . ' ' . ($request->user->nom ?? '')
                : 'Utilisateur inconnu';

            $statusEmoji = [
                'pending' => '⏳',
                'en attente' => '⏳',
                'approved' => '✅',
                'approuvé' => '✅',
                'approuvée' => '✅',
                'rejected' => '❌',
                'rejeté' => '❌',
                'rejetée' => '❌'
            ];

            $statusText = [
                'pending' => 'en attente',
                'en attente' => 'en attente',
                'approved' => 'approuvée',
                'approuvé' => 'approuvée',
                'approuvée' => 'approuvée',
                'rejected' => 'rejetée',
                'rejeté' => 'rejetée',
                'rejetée' => 'rejetée'
            ];

            $emoji = $statusEmoji[$request->status] ?? '📋';
            $text = $statusText[$request->status] ?? $request->status;

            $activities[] = [
                'created_at' => $request->created_at,
                'user_name' => $userName,
                'description' => $emoji . ' Demande organisateur ' . $text,
                'properties' => [
                    'company' => $request->company_name,
                    'status' => ucfirst($text),
                    'email' => $request->email
                ],
                'type' => 'organizer_request'
            ];
        }

        // 3. Derniers paiements effectués
        $recentPayments = Payment::with([
            'user:id,prenom,nom',
            'event:id,title'
        ])
        ->where('statut', Payment::STATUS_PAID)
        ->latest()
        ->get();

        foreach ($recentPayments as $payment) {
            $userName = $payment->user 
                ? ($payment->user->prenom ?? '') . ' ' . ($payment->user->nom ?? '')
                : 'Utilisateur inconnu';

            $activities[] = [
                'created_at' => $payment->created_at,
                'user_name' => $userName,
                'description' => '💳 Paiement effectué',
                'properties' => [
                    'event' => $payment->event->title ?? 'Événement inconnu',
                    'amount' => number_format($payment->montant, 0, ',', ' ') . ' FCFA',
                    'method' => $payment->methode_paiement ?? 'Non spécifié'
                ],
                'type' => 'payment'
            ];
        }

        // 4. Derniers événements créés
        $recentEvents = Event::with([
                'organizer:id,company_name',
                'category:id,name'
            ])
            ->latest()
            ->get();

        foreach ($recentEvents as $event) {
            $activities[] = [
                'created_at' => $event->created_at,
                'user_name' => $event->organizer->company_name ?? 'Organisateur',
                'description' => '📅 Nouvel événement créé',
                'properties' => [
                    'titre' => $event->title,
                    'catégorie' => $event->category->name ?? 'Non catégorisé'
                ],
                'type' => 'event_created'
            ];
        }

        return $activities;
    }

    /**
     * Retourne les données des méthodes de paiement pour le graphique
     */
    public function getPaymentMethodsData()
    {
        // Utiliser la constante du modèle pour plus de sécurité
        $payments = Payment::where('statut', Payment::STATUS_PAID)
            ->where('methode_paiement', 'NOT LIKE', '%simulation%')
            ->where('methode_paiement', 'NOT LIKE', '%Simulation%')
            ->select('methode_paiement', DB::raw('count(*) as count'))
            ->groupBy('methode_paiement')
            ->get();

        $labels = [];
        $data = [];
        $methodCounts = [];

        foreach ($payments as $payment) {
            $method = $payment->methode_paiement ?: 'Non spécifié';
            
            // Ignorer les méthodes de paiement contenant "simulation"
            if (stripos($method, 'simulation') !== false) {
                continue;
            }
            
            // Normaliser les noms des méthodes de paiement
            if (stripos($method, 'MTN') !== false || stripos($method, 'mtn') !== false) {
                $method = 'MTN Mobile Money';
            } elseif (stripos($method, 'Airtel') !== false || stripos($method, 'airtel') !== false) {
                $method = 'Airtel Money';
            } elseif (stripos($method, 'Mobile Money') !== false && stripos($method, 'MTN') === false && stripos($method, 'Airtel') === false) {
                $method = 'Mobile Money';
            }
            
            // Grouper les méthodes similaires
            if (!isset($methodCounts[$method])) {
                $methodCounts[$method] = 0;
            }
            $methodCounts[$method] += $payment->count;
        }

        // Convertir en tableaux pour le graphique
        foreach ($methodCounts as $method => $count) {
            $labels[] = $method;
            $data[] = $count;
        }

        // Si aucun paiement, retourner des données vides
        if (empty($labels)) {
            return response()->json([
                'labels' => ['Aucun paiement'],
                'data' => [0]
            ]);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Retourne les données des tendances de paiement pour le graphique
     */
    public function getTrendsData()
    {
        // Récupérer les 12 derniers mois
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M');
            
            // Calculer le montant total des paiements pour ce mois
            // Utiliser la constante du modèle pour plus de sécurité
            $amount = Payment::where('statut', Payment::STATUS_PAID)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('montant');
            
            $data[] = (float) ($amount ?? 0);
        }

        return response()->json([
            'labels' => $months,
            'data' => $data
        ]);
    }

    /**
     * Affiche la page des statistiques
     */
    public function stats()
    {
        // Statistiques générales
        $stats = [
            'users_count' => User::count(),
            'events_count' => Event::count(),
            'total_tickets_sold' => Ticket::withCount(['paidOrders as sold_count' => function($query) {
                $query->select(DB::raw('sum(quantity)'));
            }])->first()->sold_count ?? 0,
            'revenue' => Payment::where('statut', Payment::STATUS_PAID)->sum('montant') ?: 0,
        ];

        // Statistiques des utilisateurs par mois
        $userStats = $this->getUserStatsByMonth();

        // Statistiques des événements
        $eventStats = $this->getEventStats();

        // Statistiques des tickets
        $ticketStats = $this->getTicketStats();

        return view('dashboard.admin.stats.index', compact('stats', 'userStats', 'eventStats', 'ticketStats'));
    }

    /**
     * Affiche la page des paramètres
     */
    public function settings()
    {
        // Récupérer tous les paramètres de la base de données
        $settingsFromDB = DB::table('settings')->get();
        
        // Convertir en tableau associatif
        $settings = [];
        foreach ($settingsFromDB as $setting) {
            $settings[$setting->key] = $setting->value;
        }
        
        return view('dashboard.admin.settings.index', compact('settings'));
    }

    /**
     * Met à jour les paramètres du site
     */
    public function updateSettings(Request $request)
    {
        try {
            // Validation personnalisée pour le favicon (accepte .ico, .png, .jpg, .jpeg)
            $faviconRules = ['nullable', 'file', 'max:1024'];
            $faviconRules[] = function ($attribute, $value, $fail) {
                if ($value) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();
                    $allowedExtensions = ['ico', 'png', 'jpg', 'jpeg'];
                    $allowedMimeTypes = [
                        'image/x-icon',
                        'image/vnd.microsoft.icon',
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'application/octet-stream' // Certains fichiers .ico peuvent avoir ce type MIME
                    ];
                    
                    if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimeTypes)) {
                        $fail('Le fichier favicon doit être au format .ico, .png, .jpg ou .jpeg.');
                    }
                }
            };
            
            $validated = $request->validate([
                'site_name' => 'required|string|max:255',
                'site_description' => 'nullable|string|max:1000',
                'contact_email' => 'nullable|email',
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'facebook_url' => 'nullable|url|max:255',
                'twitter_url' => 'nullable|url|max:255',
                'instagram_url' => 'nullable|url|max:255',
                'tiktok_url' => 'nullable|url|max:255',
                'maintenance_mode' => 'nullable|boolean',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'favicon' => $faviconRules,
            ]);

            // Mise à jour des paramètres dans la table settings
            foreach ($validated as $key => $value) {
                // Ignorer les fichiers uploadés pour le moment
                if (!in_array($key, ['logo', 'favicon'])) {
                    DB::table('settings')->updateOrInsert(
                        ['key' => $key],
                        ['value' => $value ?? '', 'updated_at' => now()]
                    );
                }
            }

            // Traitement du logo
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('public/settings');
                DB::table('settings')->updateOrInsert(
                    ['key' => 'logo'],
                    ['value' => str_replace('public/', 'storage/', $logoPath), 'updated_at' => now()]
                );
                // Vider le cache des paramètres
                SettingsHelper::clearCache();
            }

            // Traitement du favicon
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('public/settings');
                DB::table('settings')->updateOrInsert(
                    ['key' => 'favicon'],
                    ['value' => str_replace('public/', 'storage/', $faviconPath), 'updated_at' => now()]
                );
                // Vider le cache des paramètres
                SettingsHelper::clearCache();
            }

            // Gestion du mode maintenance
            if (isset($validated['maintenance_mode'])) {
                // Mettre à jour la valeur dans la base de données
                DB::table('settings')->updateOrInsert(
                    ['key' => 'maintenance_mode'],
                    ['value' => $validated['maintenance_mode'] ? '1' : '0', 'updated_at' => now()]
                );
                
                if ($validated['maintenance_mode']) {
                    // Activer le mode maintenance
                    Artisan::call('down', [
                        '--render' => 'errors.maintenance',
                        '--secret' => 'admin-' . md5(config('app.key'))
                    ]);
                } else {
                    // Désactiver le mode maintenance
                    Artisan::call('up');
                }
            } else {
                // Si la case n'est pas cochée, la valeur n'est pas envoyée dans le formulaire
                // Donc on doit explicitement mettre à jour la valeur à 0
                DB::table('settings')->updateOrInsert(
                    ['key' => 'maintenance_mode'],
                    ['value' => '0', 'updated_at' => now()]
                );
                
                // Désactiver le mode maintenance
                Artisan::call('up');
            }

            // Vider le cache des paramètres
            SettingsHelper::clearCache();

            return redirect()->route('admin.settings')
                ->with('success', 'Les paramètres ont été mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Error updating settings: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour des paramètres: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les statistiques des utilisateurs par mois
     */
    private function getUserStatsByMonth()
    {
        $months = collect();
        $data = collect();
        $labels = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('Y-m'));
            $labels->push($date->format('M Y'));
        }

        $usersByMonth = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months)
            ->groupBy('month')
            ->pluck('count', 'month');

        foreach ($months as $month) {
            $data->push($usersByMonth[$month] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Récupère les statistiques des événements
     */
    private function getEventStats()
    {
        // Statistiques par catégorie
        $categoriesData = DB::table('events')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->get();

        $categoryLabels = $categoriesData->pluck('name');
        $categoryData = $categoriesData->pluck('count');

        // Statistiques par mois
        $months = collect();
        $monthLabels = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('Y-m'));
            $monthLabels->push($date->format('M Y'));
        }

        $eventsByMonth = Event::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months)
            ->groupBy('month')
            ->pluck('count', 'month');

        $monthData = collect();
        foreach ($months as $month) {
            $monthData->push($eventsByMonth[$month] ?? 0);
        }

        return [
            'category_labels' => $categoryLabels,
            'category_data' => $categoryData,
            'month_labels' => $monthLabels,
            'month_data' => $monthData
        ];
    }

    /**
     * Récupère les statistiques des tickets
     */
    private function getTicketStats()
    {
        // Statistiques par événement (top 5)
        $eventTickets = DB::table('tickets')
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->select('events.title', DB::raw('count(*) as count'))
            ->groupBy('events.title')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $eventLabels = $eventTickets->pluck('title');
        $eventData = $eventTickets->pluck('count');

        // Statistiques par jour (derniers 14 jours)
        $days = collect();
        $dayLabels = collect();

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('Y-m-d'));
            $dayLabels->push($date->format('d M'));
        }

        $ticketsByDay = Ticket::selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->whereIn(DB::raw('DATE(created_at)'), $days)
            ->groupBy('day')
            ->pluck('count', 'day');

        $dayData = collect();
        foreach ($days as $day) {
            $dayData->push($ticketsByDay[$day] ?? 0);
        }

        return [
            'event_labels' => $eventLabels,
            'event_data' => $eventData,
            'day_labels' => $dayLabels,
            'day_data' => $dayData
        ];
    }
}








