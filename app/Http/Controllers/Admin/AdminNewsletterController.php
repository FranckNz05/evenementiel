<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminNewsletterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Affiche la liste des abonnés à la newsletter
     */
    public function index()
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('is_active', true)->count(),
            'inactive' => NewsletterSubscriber::where('is_active', false)->count(),
        ];

        return view('dashboard.admin.newsletter.index', compact('subscribers', 'stats'));
    }

    /**
     * Affiche le formulaire d'envoi d'une newsletter
     */
    public function create()
    {
        $activeSubscribersCount = NewsletterSubscriber::where('is_active', true)->count();
        return view('dashboard.admin.newsletter.create', compact('activeSubscribersCount'));
    }

    /**
     * Enregistre un nouvel abonné
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'is_active' => 'boolean',
        ]);

        NewsletterSubscriber::create([
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Abonné ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un abonné
     */
    public function show($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        return view('dashboard.admin.newsletter.show', compact('subscriber'));
    }

    /**
     * Affiche le formulaire d'édition d'un abonné
     */
    public function edit($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        return view('dashboard.admin.newsletter.edit', compact('subscriber'));
    }

    /**
     * Met à jour un abonné
     */
    public function update(Request $request, $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email,' . $id,
            'is_active' => 'boolean',
        ]);

        $subscriber->update([
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Abonné mis à jour avec succès.');
    }

    /**
     * Supprime un abonné
     */
    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Abonné supprimé avec succès.');
    }

    /**
     * Exporte la liste des abonnés à la newsletter
     */
    public function export(Request $request)
    {
        try {
            $query = NewsletterSubscriber::query();

            // Filtre par statut
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $subscribers = $query->get();

            // Générer un fichier CSV
            $filename = 'newsletter_subscribers_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($subscribers) {
                $file = fopen('php://output', 'w');

                // En-têtes CSV
                fputcsv($file, ['ID', 'Email', 'Statut', 'Date d\'inscription']);

                // Données
                foreach ($subscribers as $subscriber) {
                    fputcsv($file, [
                        $subscriber->id,
                        $subscriber->email,
                        $subscriber->is_active ? 'Actif' : 'Inactif',
                        $subscriber->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting newsletter subscribers: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'exportation.');
        }
    }
}

