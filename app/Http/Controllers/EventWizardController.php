<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EventWizardController extends Controller
{
    /**
     * Affiche le formulaire de création d'événement - Étape 1 (Informations de base)
     */
    public function step1(Request $request)
    {
        $event = $request->session()->get('event_wizard', []);
        $categories = Category::all();
        $isAdmin = Auth::user()->isAdmin() || Auth::user()->hasRole(3);

        return view('events.wizard.step1', compact('event', 'categories', 'isAdmin'));
    }

    /**
     * Traite les informations de base de l'événement - Étape 1
     */
    public function postStep1(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('events')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'event_type' => ['required', Rule::in(['Espace libre', 'Plan de salle', 'Mixte'])],
            'status' => ['required', Rule::in(['Payant', 'Gratuit'])],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $validated['image'] = $path;
        }

        if ($request->has('keywords')) {
            $keywords = explode(',', $request->keywords);
            $keywords = array_map('trim', $keywords);
            $validated['keywords'] = json_encode($keywords);
        } else {
            $validated['keywords'] = null;
        }

        // Formatage des dates
        $validated['start_date'] = Carbon::parse($validated['start_date']);
        $validated['end_date'] = Carbon::parse($validated['end_date']);
        $validated['slug'] = Str::slug($validated['title']);

        $request->session()->put('event_wizard', $validated);

        return redirect()->route('events.wizard.step2');
    }

    /**
     * Affiche le formulaire de création d'événement - Étape 2 (Lieu)
     */
    public function step2(Request $request)
    {
        $event = $request->session()->get('event_wizard');

        if (!$event) {
            return redirect()->route('events.wizard.step1')
                ->with('error', 'Veuillez commencer par remplir les informations de base');
        }

        return view('events.wizard.step2', compact('event'));
    }

    /**
     * Traite les informations de lieu de l'événement - Étape 2
     */
    public function postStep2(Request $request)
    {
        $validated = $request->validate([
            'lieu' => 'required|string|max:100',
            'adresse' => 'required|string|max:191',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'adresse_map' => 'nullable|string|max:700',
        ]);

        $event = $request->session()->get('event_wizard', []);
        $mergedData = array_merge($event, $validated);
        $request->session()->put('event_wizard', $mergedData);

        return redirect()->route('events.wizard.step3');
    }

    /**
     * Affiche le formulaire de création d'événement - Étape 3 (Billets)
     */
    public function step3(Request $request)
    {
        $event = $request->session()->get('event_wizard');

        if (!$event) {
            return redirect()->route('events.wizard.step1');
        }

        $tickets = $request->session()->get('event_wizard_tickets', []);

        return view('events.wizard.step3', compact('event', 'tickets'));
    }

    /**
     * Traite les informations de billets de l'événement - Étape 3
     */
    public function postStep3(Request $request)
    {
        \Log::debug('Début de la méthode postStep3');
        $event = $request->session()->get('event_wizard');
        \Log::debug('Données de l\'événement en session (postStep3)', $event);

        if (!$event) {
            \Log::warning('Aucune donnée d\'événement trouvée en session, redirection vers step1');
            return redirect()->route('events.wizard.step1')
                ->with('error', 'Votre session a expiré. Veuillez recommencer.');
        }

        if ($event['status'] === 'Payant') {
            try {
                \Log::debug('Début de la validation des billets');
                
                // Nettoyer les données des billets avant la validation
                $ticketsData = $request->tickets;
                foreach ($ticketsData as &$ticket) {
                    // Convertir la valeur de 'reservable' en booléen
                    if (isset($ticket['reservable'])) {
                        $ticket['reservable'] = filter_var($ticket['reservable'], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $ticket['reservable'] = false;
                    }
                }
                $request->merge(['tickets' => $ticketsData]);
                
                $validated = $request->validate([
                    'tickets' => 'required|array|min:1',
                    'tickets.*.nom' => 'required|string|max:255',
                    'tickets.*.description' => 'nullable|string',
                    'tickets.*.prix' => 'required|numeric|min:0',
                    'tickets.*.quantite' => 'required|integer|min:1',
                    'tickets.*.reservable' => 'boolean',
                    'tickets.*.reservation_deadline' => 'nullable|required_if:tickets.*.reservable,1|date|after:now',
                    'tickets.*.montant_promotionnel' => 'nullable|numeric|min:0',
                    'tickets.*.promotion_start' => 'nullable|required_with:tickets.*.montant_promotionnel|date',
                    'tickets.*.promotion_end' => 'nullable|required_with:tickets.*.montant_promotionnel|date|after:tickets.*.promotion_start',
                ]);

                // Vérifier l'unicité des noms de billets
                $noms = array_column($request->tickets, 'nom');
                if (count($noms) !== count(array_unique($noms))) {
                    throw ValidationException::withMessages([
                        'tickets' => 'Les noms des billets doivent être uniques pour cet événement.'
                    ]);
                }

                $tickets = [];
                foreach ($request->tickets as $index => $ticket) {
                    $ticketData = [
                        'nom' => $ticket['nom'],
                        'description' => $ticket['description'] ?? null,
                        'prix' => $ticket['prix'],
                        'quantite' => $ticket['quantite'],
                        'reservable' => $ticket['reservable'] ?? false,
                        'reservation_deadline' => null,
                        'montant_promotionnel' => null,
                        'promotion_start' => null,
                        'promotion_end' => null,
                    ];

                    // Gestion de la date limite de réservation
                    if (($ticketData['reservable'] ?? false) && !empty($ticket['reservation_deadline'])) {
                        $ticketData['reservation_deadline'] = Carbon::parse($ticket['reservation_deadline']);
                    }

                    // Gestion des promotions
                    if (!empty($ticket['montant_promotionnel']) && $ticket['montant_promotionnel'] > 0) {
                        if (empty($ticket['promotion_start']) || empty($ticket['promotion_end'])) {
                            throw ValidationException::withMessages([
                                "tickets.$index.promotion_start" => 'Les dates de début et de fin de promotion sont requises'
                            ]);
                        }

                        $ticketData['montant_promotionnel'] = $ticket['montant_promotionnel'];
                        $ticketData['promotion_start'] = Carbon::parse($ticket['promotion_start']);
                        $ticketData['promotion_end'] = Carbon::parse($ticket['promotion_end']);

                        // Validation supplémentaire
                        if ($ticketData['promotion_end'] <= $ticketData['promotion_start']) {
                            throw ValidationException::withMessages([
                                "tickets.$index.promotion_end" => 'La date de fin doit être après la date de début'
                            ]);
                        }

                        if ($ticketData['montant_promotionnel'] >= $ticketData['prix']) {
                            throw ValidationException::withMessages([
                                "tickets.$index.montant_promotionnel" => 'Le prix promotionnel doit être inférieur au prix normal'
                            ]);
                        }
                    }

                    $tickets[] = $ticketData;
                }

                $request->session()->put('event_wizard_tickets', $tickets);
                \Log::debug('Billets enregistrés avec succès', ['count' => count($tickets)]);

            } catch (ValidationException $e) {
                \Log::error('Erreur de validation des billets', ['errors' => $e->errors()]);
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            } catch (\Exception $e) {
                \Log::error('Erreur lors du traitement des billets', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                    ->with('error', 'Une erreur est survenue lors du traitement des billets: ' . $e->getMessage())
                    ->withInput();
            }
        } else {
            $request->session()->put('event_wizard_tickets', []);
            \Log::debug('Aucun billet nécessaire pour un événement gratuit');
        }

        \Log::debug('Redirection vers l\'étape 4');
        return redirect()->route('events.wizard.step4');
    }

    /**
     * Affiche le formulaire de création d'événement - Étape 4 (Sponsors)
     */
    public function step4(Request $request)
    {
        \Log::info('Début de la méthode step4');
        $event = $request->session()->get('event_wizard');
        \Log::info('Données de l\'événement en session (step4):', $event ?? []);

        if (!$event) {
            \Log::warning('Aucune donnée d\'événement trouvée en session, redirection vers step1');
            return redirect()->route('events.wizard.step1')
                ->with('error', 'Veuillez commencer par remplir les informations de base');
        }

        $sponsors = $request->session()->get('event_wizard_sponsors', []);
        \Log::info('Sponsors en session:', $sponsors);

        return view('events.wizard.step4', compact('event', 'sponsors'));
    }

    /**
     * Traite les informations des sponsors - Étape 4
     */
    public function postStep4(Request $request)
    {
        $sponsors = [];

        if ($request->has('sponsors')) {
            $request->validate([
                'sponsors' => 'nullable|array',
                'sponsors.*.name' => 'required|string|max:255',
                'sponsors.*.logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Vérifier l'unicité des noms de sponsors
            $names = array_column($request->sponsors, 'name');
            if (count($names) !== count(array_unique($names))) {
                throw ValidationException::withMessages([
                    'sponsors' => 'Les noms des sponsors doivent être uniques pour cet événement.'
                ]);
            }

            foreach ($request->sponsors as $sponsorData) {
                $sponsor = [
                    'name' => $sponsorData['name'],
                    'logo_path' => null
                ];

                if (isset($sponsorData['logo']) && $sponsorData['logo'] instanceof \Illuminate\Http\UploadedFile) {
                    $sponsor['logo_path'] = $sponsorData['logo']->store('sponsors', 'public');
                } elseif (isset($sponsorData['existing_logo'])) {
                    $sponsor['logo_path'] = $sponsorData['existing_logo'];
                }

                $sponsors[] = $sponsor;
            }
        }

        $request->session()->put('event_wizard_sponsors', $sponsors);

        return redirect()->route('events.wizard.preview');
    }

    /**
     * Affiche la page de revue finale de l'événement
     */
    /**
     * Affiche la page de revue finale de l'événement
     */
    public function review(Request $request)
    {
        $event = $request->session()->get('event_wizard');
        $tickets = $request->session()->get('event_wizard_tickets', []);
        $sponsors = $request->session()->get('event_wizard_sponsors', []);

        if (!$event) {
            return redirect()->route('events.wizard.step1')
                ->with('error', 'Veuillez commencer par remplir les informations de base');
        }

        // Calculer la capacité totale à partir des billets
        $totalCapacity = 0;
        foreach ($tickets as &$ticket) {
            $quantity = $ticket['quantite'] ?? 0;
            $totalCapacity += (int)$quantity;
            
            // Formater les dates des tickets
            if (isset($ticket['reservation_deadline'])) {
                $ticket['reservation_deadline'] = Carbon::parse($ticket['reservation_deadline'])->format('Y-m-d H:i:s');
            }
            if (isset($ticket['promotion_start'])) {
                $ticket['promotion_start'] = Carbon::parse($ticket['promotion_start'])->format('Y-m-d');
            }
            if (isset($ticket['promotion_end'])) {
                $ticket['promotion_end'] = Carbon::parse($ticket['promotion_end'])->format('Y-m-d');
            }
        }

        // Ajouter la capacité calculée à l'événement
        $event['capacity'] = $totalCapacity > 0 ? $totalCapacity : null;

        // Gestion de l'image de l'événement
        if (isset($event['image'])) {
            $event['image'] = Storage::url($event['image']);
        }

        // Gestion des logos des sponsors
        foreach ($sponsors as &$sponsor) {
            if (isset($sponsor['logo_path'])) {
                $sponsor['logo_path'] = Storage::url($sponsor['logo_path']);
            }
        }

        // Formater les dates de l'événement
        if (isset($event['start_date'])) {
            $event['start_date'] = Carbon::parse($event['start_date'])->format('Y-m-d H:i:s');
        }
        if (isset($event['end_date'])) {
            $event['end_date'] = Carbon::parse($event['end_date'])->format('Y-m-d H:i:s');
        }

        $organizer = Auth::user()->organizer;

        return view('events.wizard.preview', compact('event', 'tickets', 'sponsors', 'organizer'));
    }

    /**
     * Traite la soumission finale de l'événement depuis la page de prévisualisation
     */
    public function storeComplete(Request $request)
    {
        $eventData = $request->session()->get('event_wizard');
        $tickets = $request->session()->get('event_wizard_tickets', []);
        $sponsors = $request->session()->get('event_wizard_sponsors', []);

        
        
        if (!$eventData) {
            return redirect()->route('events.wizard.step1')
                ->with('error', 'Votre session a expiré. Veuillez recommencer la création de votre événement.');
        }
        
        // Vérification de l'unicité du titre avant création
    if (Event::where('title', $eventData['title'])->exists()) {
        return redirect()->route('events.wizard.step1')
            ->with('error', 'Un événement avec ce titre existe déjà. Veuillez choisir un titre différent.')
            ->withInput();
    }

        // Vérification que tous les champs requis sont présents
        $requiredFields = [
            'title' => 'titre',
            'description' => 'description',
            'category_id' => 'catégorie',
            'start_date' => 'date de début',
            'end_date' => 'date de fin',
            'event_type' => 'type d\'événement',
            'lieu' => 'lieu',
            'adresse' => 'adresse',
            'ville' => 'ville',
            'pays' => 'pays',
            'image' => 'image'
        ];

        foreach ($requiredFields as $field => $label) {
            if (!isset($eventData[$field])) {
                return redirect()->route('events.wizard.step1')
                    ->with('error', "Le champ '$label' est obligatoire. Veuillez le remplir.");
            }
        }

        DB::beginTransaction();

        try {
            // Création de l'événement
            $event = new Event();
            
            // Préparation des données de l'événement
            $user = Auth::user();
            
            // Gérer le profil d'organisateur
            if (!$user->organizer) {
                // Si l'utilisateur n'a pas de profil d'organisateur, en créer un
                $organizer = \App\Models\Organizer::create([
                    'user_id' => $user->id,
                    'company_name' => $user->prenom . ' ' . $user->nom,
                    'email' => $user->email,
                    'phone_primary' => $user->phone ?? '',
                    'slug' => \Illuminate\Support\Str::slug($user->prenom . ' ' . $user->nom . '-' . $user->id),
                    'is_verified' => true,
                ]);
                $eventData['organizer_id'] = $organizer->id;
            } else {
                $eventData['organizer_id'] = $user->organizer->id;
            }
            
            $eventData['is_approved'] = $user->isAdmin() || $user->hasRole(3);
            $eventData['is_published'] = $user->isAdmin() || $user->hasRole(3);
            $eventData['etat'] = ($user->isAdmin() || $user->hasRole(3)) ? 'En cours' : 'En attente';
            
            // S'assurer que l'image est correctement stockée
            if (isset($eventData['image'])) {
                // Vérifier si l'image existe dans le stockage
                if (!Storage::disk('public')->exists($eventData['image'])) {
                    throw new \Exception("L'image de votre événement n'a pas pu être trouvée. Veuillez réessayer de télécharger l'image.");
                }
            }

            // Générer un slug unique
            $baseSlug = Str::slug($eventData['title']);
            $slug = $baseSlug;
            $counter = 1;
            
            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $eventData['slug'] = $slug;
            
            $event->fill($eventData);
            $event->save();

            // Création des billets
            if ($eventData['status'] === 'Payant') {
                foreach ($tickets as $ticketData) {
                    $event->tickets()->create($ticketData);
                }
            }

            // Création des sponsors
            foreach ($sponsors as $sponsorData) {
                $event->sponsors()->create($sponsorData);
            }

            // Demande de publication si nécessaire
            $isAdmin = $user->isAdmin() || $user->hasRole(3);
            if (!$isAdmin && class_exists('App\Models\EventPublicationRequest')) {
                \App\Models\EventPublicationRequest::create([
                    'user_id' => Auth::id(),
                    'event_id' => $event->id,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Nettoyer la session
            $request->session()->forget([
                'event_wizard',
                'event_wizard_tickets',
                'event_wizard_sponsors'
            ]);

            // Stocker l'ID de l'événement dans la session pour la page de confirmation
            $request->session()->flash('created_event_id', $event->id);

            return redirect()->route('events.wizard.complete')
                ->with('success', 'Félicitations ! Votre événement a été créé avec succès. Il est maintenant en attente d\'approbation par nos administrateurs.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Gestion des erreurs de contrainte d'unicité
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'events_slug_unique')) {
                    return redirect()->back()
                        ->with('error', 'Il existe déjà un événement avec un titre similaire. Veuillez choisir un titre différent.')
                        ->withInput();
                }
            }
            
            \Log::error('Erreur base de données lors de la création de l\'événement: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Nous n\'avons pas pu créer votre événement. Veuillez vérifier vos informations et réessayer.')
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création événement: ' . $e->getMessage());
            
            // Messages d'erreur personnalisés selon le type d'erreur
            $errorMessage = 'Une erreur est survenue lors de la création de votre événement. ';
            
            if (str_contains($e->getMessage(), 'image')) {
                $errorMessage = 'Il y a eu un problème avec l\'image de votre événement. Veuillez réessayer de télécharger une image.';
            } elseif (str_contains($e->getMessage(), 'date')) {
                $errorMessage = 'Il y a un problème avec les dates de votre événement. Veuillez vérifier les dates saisies.';
            } elseif (str_contains($e->getMessage(), 'ticket')) {
                $errorMessage = 'Il y a un problème avec les informations des billets. Veuillez vérifier les prix et les quantités.';
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Affiche la page de confirmation
     */
    public function complete()
    {
        return view('events.wizard.complete');
    }

    /**
     * Affiche l'étape spécifiée
     */
    public function showStep($step)
    {
        switch ($step) {
            case 1: return $this->step1(request());
            case 2: return $this->step2(request());
            case 3: return $this->step3(request());
            case 4: return $this->step4(request());
            default: return redirect()->route('events.wizard.step1');
        }
    }
    
     /**
     * Enregistre un message de débogage dans les logs de Laravel
     *
     * @param string $message Le message à enregistrer
     * @param array $data Données supplémentaires à enregistrer
     * @return void
     */
    private function logDebug($message, array $data = [])
    {
        if (!empty($data)) {
            \Log::debug($message, $data);
        } else {
            \Log::debug($message);
        }
    }
}
