<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\User;
use App\Mail\WithdrawalApproved;
use App\Mail\WithdrawalApprovedAdmin;
use App\Mail\WithdrawalRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:Administrateur');
    }

    /**
     * Affiche l'historique de tous les retraits
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('organizer');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $query->whereHas('organizer', function($q) use ($request) {
                $q->where('prenom', 'like', '%' . $request->search . '%')
                  ->orWhere('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhere('phone_number', 'like', '%' . $request->search . '%')
              ->orWhere('transaction_reference', 'like', '%' . $request->search . '%');
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $stats = [
            'total_withdrawals' => Withdrawal::count(),
            'total_amount' => Withdrawal::where('status', 'completed')->sum('amount'),
            'completed_count' => Withdrawal::where('status', 'completed')->count(),
            'today_amount' => Withdrawal::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'today_count' => Withdrawal::whereDate('created_at', today())->count(),
        ];

        // Statistiques par méthode de paiement
        $paymentMethodsStats = Withdrawal::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        return view('dashboard.admin.withdrawals.index', compact('withdrawals', 'stats', 'paymentMethodsStats'));
    }

    /**
     * Affiche les détails d'un retrait
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('organizer', 'processedBy');
        
        // Si c'est une requête AJAX (pour les modals), retourner du JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'withdrawal' => $withdrawal
            ]);
        }
        
        return view('dashboard.admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approuve et traite un retrait
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'pin' => 'required|string|regex:/^\d{4}$/',
        ], [
            'pin.required' => 'Le code PIN Airtel Money est requis',
            'pin.regex' => 'Le code PIN doit contenir exactement 4 chiffres',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Ce retrait ne peut plus être traité.');
        }

        DB::beginTransaction();

        try {
            // Marquer le retrait comme en cours de traitement
            $withdrawal->update([
                'status' => 'processing',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Traiter le retrait selon la méthode de paiement
            if ($withdrawal->payment_method === 'Airtel Money') {
                // S'assurer que la connexion est active avant l'appel API
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $dbCheckException) {
                    DB::purge();
                    DB::connection()->reconnect();
                }
                
                $airtelService = app(\App\Services\AirtelMoneyService::class);

                // Appel API dans un try-catch séparé pour mieux gérer les erreurs
                try {
                    $result = $airtelService->disburse([
                        'phone' => $withdrawal->phone_number,
                        'amount' => $withdrawal->amount,
                        'pin' => $request->pin,
                        'reference' => $withdrawal->transaction_reference,
                        'transaction_id' => 'WD-' . $withdrawal->id . '-' . time(),
                        'wallet_type' => 'SALARY', // Selon la documentation Disbursement-APIs v3.0
                        'transaction_type' => 'B2B', // Selon la documentation Disbursement-APIs v3.0
                    ]);
                } catch (\Exception $apiException) {
                    // Reconnecter à la base de données en cas d'erreur API
                    try {
                        DB::connection()->getPdo();
                    } catch (\Exception $dbException) {
                        DB::purge();
                        DB::connection()->reconnect();
                    }
                    
                    // Relancer l'exception pour qu'elle soit gérée par le catch principal
                    throw $apiException;
                }

                // Reconnecter à la base de données après l'appel API (au cas où la connexion aurait expiré)
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $dbException) {
                    DB::purge();
                    DB::connection()->reconnect();
                }

                // Accepter les deux codes de succès selon la documentation
                if ($result['success'] && (($result['status'] ?? null) === 'success' || in_array($result['response_code'] ?? null, ['DP00800001001', 'DP00900001001']))) {
                    // Retrait réussi
                    $withdrawal->update([
                        'status' => 'completed',
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'airtel_money_id' => $result['airtel_money_id'] ?? null,
                        'reference_id' => $result['reference_id'] ?? null,
                        'details' => [
                            'airtel_response' => $result,
                            'processed_by' => auth()->user()->name,
                            'processed_at' => now()->toISOString(),
                        ],
                    ]);


                    DB::commit();

                    // Envoyer l'email à l'organisateur
                    try {
                        Mail::to($withdrawal->organizer->email)->send(new WithdrawalApproved($withdrawal, auth()->user()));
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de l\'email d\'approbation à l\'organisateur', [
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Envoyer l'email de confirmation à tous les administrateurs
                    try {
                        User::role('Administrateur')->each(function($admin) use ($withdrawal) {
                            Mail::to($admin->email)->send(new WithdrawalApprovedAdmin($withdrawal, auth()->user()));
                        });
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de l\'email de confirmation aux administrateurs', [
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Message de succès avec détails Airtel
                    $successMessage = 'Retrait approuvé et traité avec succès. L\'argent a été envoyé à ' . $withdrawal->phone_number;
                    if (!empty($result['transaction_id'])) {
                        $successMessage .= ' (Transaction ID: ' . $result['transaction_id'] . ')';
                    }
                    if (!empty($result['airtel_money_id'])) {
                        $successMessage .= ' (Airtel Money ID: ' . $result['airtel_money_id'] . ')';
                    }
                    if (!empty($result['response_code'])) {
                        $successMessage .= ' [Code: ' . $result['response_code'] . ']';
                    }
                    
                    return back()->with('success', $successMessage);
                } else {
                    // Retrait échoué
                    // Reconnecter si nécessaire avant la mise à jour
                    try {
                        DB::connection()->getPdo();
                    } catch (\Exception $dbException) {
                        DB::purge();
                        DB::connection()->reconnect();
                    }
                    
                    // Préparer les détails complets avec la réponse Airtel
                    $details = [
                        'airtel_response' => $result,
                        'processed_by' => auth()->user()->name,
                        'error_at' => now()->toISOString(),
                    ];
                    
                    // Ajouter la réponse brute d'Airtel si disponible
                    if (isset($result['raw_response'])) {
                        $details['airtel_raw_response'] = $result['raw_response'];
                    }
                    
                    // Ajouter le contenu brut de la réponse HTTP si disponible
                    if (isset($result['raw_body'])) {
                        $details['airtel_raw_body'] = $result['raw_body'];
                    }
                    
                    // Ajouter le message d'Airtel si disponible
                    if (isset($result['airtel_message'])) {
                        $details['airtel_message'] = $result['airtel_message'];
                    }
                    
                    // Logger la réponse complète d'Airtel
                    Log::error('Retrait Airtel Money échoué - Réponse complète', [
                        'withdrawal_id' => $withdrawal->id,
                        'phone' => $withdrawal->phone_number,
                        'amount' => $withdrawal->amount,
                        'airtel_response' => $result,
                        'raw_response' => $result['raw_response'] ?? null,
                        'raw_body' => $result['raw_body'] ?? null,
                        'airtel_message' => $result['airtel_message'] ?? $result['message'] ?? null,
                        'response_code' => $result['response_code'] ?? null,
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'status' => $result['status'] ?? null,
                        'message' => $result['message'] ?? null,
                    ]);
                    
                    $withdrawal->update([
                        'status' => 'rejected',
                        'rejection_reason' => $result['message'] ?? 'Erreur lors du traitement Airtel Money',
                        'details' => $details,
                    ]);


                    DB::commit();

                    // Envoyer l'email à l'organisateur (rejeté à cause de l'erreur)
                    try {
                        Mail::to($withdrawal->organizer->email)->send(new WithdrawalRejected($withdrawal, auth()->user()));
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de l\'email de rejet à l\'organisateur', [
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Message d'erreur détaillé avec informations Airtel
                    $errorMessage = 'Erreur lors du retrait';
                    
                    // Ajouter le message d'erreur d'Airtel
                    if (!empty($result['message'])) {
                        $errorMessage .= ': ' . $result['message'];
                    } else {
                        $errorMessage .= ': Erreur inconnue';
                    }
                    
                    // Ajouter le code d'erreur Airtel si disponible
                    if (!empty($result['response_code'])) {
                        $errorMessage .= ' [Code: ' . $result['response_code'] . ']';
                    }
                    
                    // Ajouter l'ID de transaction si disponible
                    if (!empty($result['transaction_id'])) {
                        $errorMessage .= ' (Transaction ID: ' . $result['transaction_id'] . ')';
                    }
                    
                    // Ajouter des informations supplémentaires selon le code d'erreur
                    if (!empty($result['response_code'])) {
                        $errorCode = $result['response_code'];
                        if ($errorCode === 'DP00900001007') {
                            $errorMessage .= ' - Solde insuffisant dans le wallet Partner';
                        } elseif ($errorCode === 'DP00900001019' || $errorCode === 'DP00800001010') {
                            $errorMessage .= ' - Le destinataire est barré ou non autorisé';
                        } elseif ($errorCode === 'DP00900001012') {
                            $errorMessage .= ' - Numéro de téléphone invalide ou non enregistré';
                        } elseif ($errorCode === 'DP00900001003' || $errorCode === 'DP00900001004') {
                            $errorMessage .= ' - Montant hors limites autorisées';
                        } elseif ($errorCode === 'DP00800001024') {
                            $errorMessage .= ' - Timeout: La transaction a expiré';
                        }
                    }
                    
                    return back()->with('error', $errorMessage);
                }
            } else {
                // Pour MTN ou autres méthodes (simulation)
                $withdrawal->update([
                    'status' => 'completed',
                    'details' => [
                        'processed_by' => auth()->user()->name,
                        'processed_at' => now()->toISOString(),
                        'method' => $withdrawal->payment_method,
                    ],
                ]);


                DB::commit();

                // Envoyer l'email à l'organisateur
                try {
                    Mail::to($withdrawal->organizer->email)->send(new WithdrawalApproved($withdrawal, auth()->user()));
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de l\'email d\'approbation à l\'organisateur', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Envoyer l'email de confirmation à tous les administrateurs
                try {
                    User::role('Administrateur')->each(function($admin) use ($withdrawal) {
                        Mail::to($admin->email)->send(new WithdrawalApprovedAdmin($withdrawal, auth()->user()));
                    });
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de l\'email de confirmation aux administrateurs', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                return back()->with('success', 'Retrait approuvé et traité avec succès.');
            }

        } catch (\Exception $e) {
            // Gérer spécifiquement l'erreur "MySQL server has gone away"
            $isDbError = str_contains($e->getMessage(), 'MySQL server has gone away') || 
                        str_contains($e->getMessage(), '2006');
            
            // Essayer de récupérer le résultat d'Airtel si disponible dans l'exception
            $airtelResult = null;
            if (method_exists($e, 'getResult') && $e->getResult()) {
                $airtelResult = $e->getResult();
            }
            
            // Logger l'exception avec toutes les informations disponibles
            Log::error('Exception lors du traitement du retrait Airtel Money', [
                'withdrawal_id' => $withdrawal->id,
                'phone' => $withdrawal->phone_number,
                'amount' => $withdrawal->amount,
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'airtel_result' => $airtelResult,
                'is_db_error' => $isDbError,
            ]);
            
            try {
                if ($isDbError) {
                    // Reconnecter à la base de données
                    DB::purge();
                    DB::connection()->reconnect();
                }
                
                // Essayer de faire le rollback si on est encore dans une transaction
                try {
                    DB::rollBack();
                } catch (\Exception $rollbackException) {
                    // Si le rollback échoue, reconnecter et réessayer
                    if ($isDbError) {
                        DB::purge();
                        DB::connection()->reconnect();
                        try {
                            DB::rollBack();
                        } catch (\Exception $retryException) {
                            // Ignorer si ça échoue encore
                        }
                    }
                }
                
                // Recharger le modèle pour s'assurer qu'on a la dernière version
                $withdrawal->refresh();
                
                // Préparer les détails avec la réponse Airtel si disponible
                $details = [
                    'processed_by' => auth()->user()->name ?? 'System',
                    'error_at' => now()->toISOString(),
                    'exception_message' => $e->getMessage(),
                ];
                
                if ($airtelResult) {
                    $details['airtel_response'] = $airtelResult;
                    if (isset($airtelResult['raw_response'])) {
                        $details['airtel_raw_response'] = $airtelResult['raw_response'];
                    }
                }
                
                // Mettre à jour le statut avec les détails complets
                $withdrawal->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Erreur technique: ' . $e->getMessage(),
                    'details' => $details,
                ]);
            } catch (\Exception $updateException) {
                // Si la mise à jour échoue aussi, logger l'erreur
                Log::error('Erreur critique lors de la gestion du retrait', [
                    'withdrawal_id' => $withdrawal->id,
                    'original_error' => $e->getMessage(),
                    'update_error' => $updateException->getMessage(),
                    'airtel_result' => $airtelResult,
                ]);
            }

            // Message d'erreur pour les exceptions
            $errorMessage = $isDbError 
                ? 'Erreur de connexion à la base de données. Veuillez réessayer.'
                : 'Erreur lors du traitement: ' . $e->getMessage();
            
            // Identifier le type d'erreur selon le message
            $exceptionMessage = $e->getMessage();
            if (str_contains($exceptionMessage, 'zéro') || str_contains($exceptionMessage, 'zero')) {
                $errorMessage .= ' - Le montant ne peut pas être zéro';
            } elseif (str_contains($exceptionMessage, 'négatif') || str_contains($exceptionMessage, 'negative')) {
                $errorMessage .= ' - Le montant ne peut pas être négatif';
            } elseif (str_contains($exceptionMessage, 'inférieur') || str_contains($exceptionMessage, 'minimum')) {
                $errorMessage .= ' - Le montant est inférieur au minimum autorisé';
            } elseif (str_contains($exceptionMessage, 'dépasse') || str_contains($exceptionMessage, 'maximum')) {
                $errorMessage .= ' - Le montant dépasse le maximum autorisé';
            } elseif (str_contains($exceptionMessage, 'barré') || str_contains($exceptionMessage, 'barred')) {
                $errorMessage .= ' - Le destinataire est barré';
            } elseif (str_contains($exceptionMessage, 'enregistré') || str_contains($exceptionMessage, 'registered')) {
                $errorMessage .= ' - Le wallet n\'est pas enregistré sur Airtel Money';
            }
                
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Refuse un retrait
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'La raison du rejet est obligatoire',
            'rejection_reason.min' => 'La raison doit contenir au moins 5 caractères',
            'rejection_reason.max' => 'La raison ne peut pas dépasser 500 caractères',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Ce retrait ne peut plus être traité.');
        }

        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'details' => [
                'rejected_by' => auth()->user()->name,
                'rejected_at' => now()->toISOString(),
                'reason' => $request->rejection_reason,
            ],
        ]);


        // Envoyer l'email à l'organisateur
        try {
            Mail::to($withdrawal->organizer->email)->send(new WithdrawalRejected($withdrawal, auth()->user()));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de rejet à l\'organisateur', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Retrait rejeté avec succès.');
    }
}
