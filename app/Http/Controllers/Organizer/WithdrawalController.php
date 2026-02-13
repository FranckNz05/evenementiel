<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\User;
use App\Services\CommissionService;
use App\Mail\WithdrawalRequested;
use App\Mail\WithdrawalPendingAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WithdrawalController extends Controller
{
    protected $commissionService;
    
    // Limite maximale de retrait par transaction (limite système)
    const MAX_WITHDRAWAL_AMOUNT = 50000000; // 50 000 000 FCFA

    public function __construct(CommissionService $commissionService)
    {
        $this->middleware(['auth', 'verified']);
        $this->commissionService = $commissionService;
    }

    /**
     * Affiche la liste des retraits de l'organisateur
     */
    public function index()
    {
        $organizer = Auth::user()->organizer;
        
        if (!$organizer) {
            return redirect()->route('organizer.dashboard')->with('error', 'Profil organisateur introuvable.');
        }
        
        $withdrawals = Withdrawal::where('organizer_id', $organizer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $revenueData = $this->commissionService->calculateOrganizerTotalNetRevenue($organizer->id);
        
        // Calculer le solde disponible
        // Seuls les retraits complétés sont déduits du solde
        // Les retraits en "processing" ne sont pas déduits tant qu'Airtel n'a pas confirmé
        $totalWithdrawn = Withdrawal::where('organizer_id', $organizer->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $availableBalance = $revenueData['net_revenue'] - $totalWithdrawn;

        return view('dashboard.organizer.withdrawals.index', compact('withdrawals', 'revenueData', 'availableBalance'));
    }

    /**
     * Vérifie le statut d'un retrait Airtel Money
     * 
     * @param Withdrawal $withdrawal
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Withdrawal $withdrawal)
    {
        // Vérifier que l'utilisateur est propriétaire du retrait
        if (Auth::id() !== $withdrawal->organizer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Vérifier que c'est un retrait Airtel Money avec un transaction_id
        if ($withdrawal->payment_method !== 'Airtel Money' || !$withdrawal->transaction_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce retrait ne peut pas être vérifié via l\'API Airtel'
            ], 400);
        }

        try {
            $airtelService = app(\App\Services\AirtelMoneyService::class);
            
            $result = $airtelService->checkDisbursementStatus($withdrawal->transaction_id);

            // Mettre à jour le retrait si le statut a changé
            if ($result['success'] && ($result['status'] ?? null) === 'success') {
                if ($withdrawal->status !== 'completed') {
                    $withdrawal->update([
                        'status' => 'completed',
                        'airtel_money_id' => $result['airtel_money_id'] ?? $withdrawal->airtel_money_id,
                        'reference_id' => $result['reference_id'] ?? $withdrawal->reference_id,
                        'processed_at' => now(),
                        'details' => json_encode(array_merge(
                            json_decode($withdrawal->details ?? '{}', true) ?: [],
                            [
                                'last_status_check' => now()->toISOString(),
                                'airtel_status' => $result,
                            ]
                        )),
                    ]);
                }
            } elseif (!$result['success'] && in_array($result['status'] ?? null, ['failed', 'refused', 'expired'])) {
                if ($withdrawal->status !== 'rejected') {
                    $withdrawal->update([
                        'status' => 'rejected',
                        'rejection_reason' => $result['message'] ?? 'Transaction échouée',
                        'details' => json_encode(array_merge(
                            json_decode($withdrawal->details ?? '{}', true) ?: [],
                            [
                                'last_status_check' => now()->toISOString(),
                                'airtel_status' => $result,
                            ]
                        )),
                    ]);
                }
            }

            return response()->json([
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? 'unknown',
                'message' => $result['message'] ?? 'Statut vérifié',
                'withdrawal_status' => $withdrawal->fresh()->status,
                'transaction_id' => $result['transaction_id'] ?? null,
                'airtel_money_id' => $result['airtel_money_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut du retrait', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche le formulaire de demande de retrait
     */
    public function create()
    {
        $organizer = Auth::user()->organizer;
        
        if (!$organizer) {
            return redirect()->route('organizer.dashboard')->with('error', 'Profil organisateur introuvable.');
        }
        
        $revenueData = $this->commissionService->calculateOrganizerTotalNetRevenue($organizer->id);
        
        // Calculer le solde disponible
        // Seuls les retraits complétés sont déduits du solde
        // Les retraits en "processing" ne sont pas déduits tant qu'Airtel n'a pas confirmé
        $totalWithdrawn = Withdrawal::where('organizer_id', $organizer->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $availableBalance = $revenueData['net_revenue'] - $totalWithdrawn;
        
        // Le maximum retirable est le minimum entre le solde disponible et la limite système
        $maxWithdrawableAmount = min($availableBalance, self::MAX_WITHDRAWAL_AMOUNT);

        return view('dashboard.organizer.withdrawals.create', compact('availableBalance', 'revenueData', 'maxWithdrawableAmount'));
    }

    /**
     * Enregistre une demande de retrait
     */
    public function store(Request $request)
    {
        $organizer = Auth::user()->organizer;
        
        if (!$organizer) {
            return back()->with('error', 'Profil organisateur introuvable.');
        }
        
        // Calculer le solde disponible et le maximum retirable
        $revenueData = $this->commissionService->calculateOrganizerTotalNetRevenue($organizer->id);
        // Seuls les retraits complétés sont déduits du solde
        $totalWithdrawn = Withdrawal::where('organizer_id', $organizer->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $availableBalance = $revenueData['net_revenue'] - $totalWithdrawn;
        // Le maximum retirable est le minimum entre le solde disponible et la limite système
        $maxWithdrawableAmount = min($availableBalance, self::MAX_WITHDRAWAL_AMOUNT);
        
        $request->validate([
            'amount' => 'required|numeric|min:100|max:' . $maxWithdrawableAmount,
            'payment_method' => 'required|in:MTN Mobile Money,Airtel Money',
            'phone_number' => ['required', 'string', 'regex:/^0(4|5|6)\d{7}$/'],
        ], [
            'amount.required' => 'Le montant est requis',
            'amount.min' => 'Le montant minimum est de 100 FCFA',
            'amount.max' => 'Le montant maximum est de ' . number_format($maxWithdrawableAmount, 0, ',', ' ') . ' FCFA (limite système: ' . number_format(self::MAX_WITHDRAWAL_AMOUNT, 0, ',', ' ') . ' FCFA par transaction)',
            'payment_method.required' => 'La méthode de paiement est requise',
            'phone_number.required' => 'Le numéro de téléphone est requis',
            'phone_number.regex' => 'Le numéro doit commencer par 05, 06 ou 04 et contenir 9 chiffres',
        ]);

        if ($request->amount > $maxWithdrawableAmount) {
            if ($availableBalance > self::MAX_WITHDRAWAL_AMOUNT) {
                return back()->with('error', 'Le montant maximum par transaction est de ' . number_format(self::MAX_WITHDRAWAL_AMOUNT, 0, ',', ' ') . ' FCFA. Vous devrez effectuer plusieurs retraits pour retirer tout votre solde de ' . number_format($availableBalance, 0, ',', ' ') . ' FCFA.');
            } else {
                return back()->with('error', 'Solde insuffisant. Votre solde disponible est de ' . number_format($availableBalance, 0, ',', ' ') . ' FCFA');
            }
        }

        DB::beginTransaction();

        try {
            // Créer le retrait en statut "pending" (en attente d'approbation admin)
            $withdrawal = Withdrawal::create([
                'organizer_id' => $organizer->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'phone_number' => $request->phone_number,
                'status' => 'pending',
                'transaction_reference' => 'WD-' . strtoupper(uniqid()),
            ]);

            DB::commit();

            // Envoyer l'email à l'organisateur
            try {
                Mail::to($organizer->email)->send(new WithdrawalRequested($withdrawal));
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email de demande de retrait à l\'organisateur', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Envoyer l'email aux administrateurs
            try {
                User::role('Administrateur')->each(function($admin) use ($withdrawal) {
                    Mail::to($admin->email)->send(new WithdrawalPendingAdmin($withdrawal));
                });
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email de demande de retrait aux administrateurs', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('organizer.withdrawals.index')
                ->with('success', 'Votre demande de retrait de ' . number_format($request->amount, 0, ',', ' ') . ' FCFA a été soumise avec succès. Elle sera traitée par un administrateur dans les plus brefs délais.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erreur lors du retrait', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'organizer_id' => $organizer->id,
                'amount' => $request->amount
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors du retrait: ' . $e->getMessage());
        }
    }
}
