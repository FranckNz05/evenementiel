<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomOfferPurchase;

class CustomEventsController extends Controller
{
    public function index(Request $request)
    {
        $selectedPlan = $request->query('plan');
        return view('custom-events.index', compact('selectedPlan'));
    }

    public function payment(Request $request)
    {
        $plan = $request->query('plan');
        $plans = [
            'start' => ['label' => 'Start', 'price' => 30000],
            'standard' => ['label' => 'Standard', 'price' => 50000],
            'premium' => ['label' => 'Premium', 'price' => 75000],
            'ultimate' => ['label' => 'Ultimate', 'price' => 100000],
        ];

        abort_unless(isset($plans[$plan]), 404);

        return view('custom-events.payment', [
            'planKey' => $plan,
            'plan' => $plans[$plan],
            'plans' => $plans,
        ]);
    }

    public function processPayment(Request $request)
    {
        $plans = [
            'start' => ['label' => 'Start', 'price' => 30000],
            'standard' => ['label' => 'Standard', 'price' => 50000],
            'premium' => ['label' => 'Premium', 'price' => 75000],
            'ultimate' => ['label' => 'Ultimate', 'price' => 100000],
        ];

        $validated = $request->validate([
            'plan' => ['required', 'in:start,standard,premium,ultimate'],
            'operator' => ['required', 'in:airtel,mtn'],
            // Numéros acceptés: 05xxxxxxx, 06xxxxxxx, 04xxxxxxx
            'phone' => ['required', 'regex:/^0(4|5|6)\d{7}$/'],
        ], [
            'phone.regex' => 'Le numéro doit commencer par 05, 06 ou 04 et contenir 9 chiffres.',
        ]);

        $planKey = $validated['plan'];
        $plan = $plans[$planKey];

        // Si c'est Airtel Money, utiliser l'API Airtel directement
        if ($validated['operator'] === 'airtel') {
            try {
                $airtelService = app(\App\Services\AirtelMoneyService::class);
                
                $result = $airtelService->initiatePayment([
                    'phone' => $validated['phone'],
                    'amount' => $plan['price'],
                    'reference' => 'CUSTOM-' . strtoupper($planKey) . '-' . time(),
                    'transaction_id' => 'TXN-' . time() . '-' . auth()->id(),
                ]);

                // Code d'erreur DP00800001001 = Success selon la documentation Airtel
                if ($result['success'] && (($result['status'] ?? null) === 'success' || $result['response_code'] === 'DP00800001001')) {
                    // Paiement réussi
                    $purchase = CustomOfferPurchase::create([
                        'user_id' => auth()->id(),
                        'plan' => $planKey,
                        'price' => $plan['price'],
                        'operator' => $validated['operator'],
                        'phone' => $validated['phone'],
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'status' => 'completed',
                    ]);

                    session(['custom_offer' => [
                        'plan' => $planKey,
                        'label' => $plan['label'],
                        'price' => $plan['price'],
                        'purchase_id' => $purchase->id,
                    ]]);

                    return redirect()->route('custom-offers.confirmation', [
                        'plan' => $planKey,
                        'operator' => $validated['operator'],
                        'purchase' => $purchase->id,
                    ])->with('success', 'Paiement Airtel Money réussi pour la formule ' . $plan['label']);
                } else {
                    // Paiement en attente ou échoué
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $result['message'] ?? 'Le paiement Airtel Money est en attente. Veuillez confirmer sur votre téléphone.');
                }
            } catch (\Exception $e) {
                \Log::error('Erreur paiement Airtel Money', [
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id(),
                    'plan' => $planKey
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erreur lors du paiement Airtel Money: ' . $e->getMessage());
            }
        }

        // Pour MTN ou autres, bloquer avec un message
        if ($validated['operator'] !== 'airtel') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
        }
    }

    public function confirmation(Request $request)
    {
        $plan = $request->query('plan');
        $operator = $request->query('operator');
        $purchaseId = $request->query('purchase');
        $plans = [
            'start' => ['label' => 'Start', 'price' => 30000],
            'standard' => ['label' => 'Standard', 'price' => 50000],
            'premium' => ['label' => 'Premium', 'price' => 75000],
            'ultimate' => ['label' => 'Ultimate', 'price' => 100000],
        ];

        abort_unless(isset($plans[$plan]), 404);

        return view('custom-events.confirmation', [
            'planKey' => $plan,
            'plan' => $plans[$plan],
            'operator' => $operator,
            'purchaseId' => $purchaseId,
        ]);
    }
}


