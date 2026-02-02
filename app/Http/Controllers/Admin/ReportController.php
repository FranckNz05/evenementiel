<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CustomPersonalEvent;
use App\Models\CustomOfferPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\EventsExport;
use App\Exports\OrdersExport;
use App\Exports\AdminPaymentsExport;

class ReportController extends Controller
{
    /**
     * Affiche la page des rapports
     */
    public function index()
    {
        // Statistiques pour la page des rapports
        $stats = [
            'users_count' => User::count(),
            'events_count' => Event::count(),
            'orders_count' => Order::count(),
            'payments_count' => Payment::count(),
        ];

        return view('dashboard.admin.reports.index', compact('stats'));
    }

    /**
     * Générer un rapport
     */
    public function generate(Request $request)
    {
        // Validation de base
        $rules = [
            'report_type' => 'required|in:events,users,orders,payments,custom_events,custom_offers',
            'date_range' => 'required|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,custom',
            'format' => 'required|in:pdf',
        ];

        // Ajouter les règles de validation pour les dates personnalisées uniquement si nécessaire
        if ($request->date_range === 'custom') {
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
        } else {
            // Si ce n'est pas custom, on ignore complètement les champs de date
            $rules['start_date'] = 'nullable';
            $rules['end_date'] = 'nullable';
        }

        $validated = $request->validate($rules, [
            'report_type.required' => 'Le type de rapport est requis.',
            'report_type.in' => 'Le type de rapport sélectionné n\'est pas valide.',
            'date_range.required' => 'La période est requise.',
            'date_range.in' => 'La période sélectionnée n\'est pas valide.',
            'start_date.required' => 'La date de début est requise pour une période personnalisée.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'end_date.required' => 'La date de fin est requise pour une période personnalisée.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être supérieure ou égale à la date de début.',
            'format.required' => 'Le format d\'export est requis.',
            'format.in' => 'Le format sélectionné n\'est pas valide.',
        ]);

        // Déterminer les dates de début et de fin
        list($startDate, $endDate, $dateRangeText) = $this->getDateRange($validated['date_range'], $request->start_date, $request->end_date);

        // Récupérer les données en fonction du type de rapport
        $data = $this->getReportData($validated['report_type'], $startDate, $endDate);

        // Générer le titre du rapport
        $title = $this->getReportTitle($validated['report_type']);

        // Générer le rapport PDF
        return $this->generatePdfReport($validated['report_type'], $data, $title, $dateRangeText);
    }

    /**
     * Détermine les dates de début et de fin en fonction de la période sélectionnée
     */
    private function getDateRange($dateRange, $startDate = null, $endDate = null)
    {
        $dateRangeText = '';

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                $dateRangeText = 'Aujourd\'hui (' . $startDate->format('d/m/Y') . ')';
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                $dateRangeText = 'Hier (' . $startDate->format('d/m/Y') . ')';
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $dateRangeText = 'Cette semaine (' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y') . ')';
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                $dateRangeText = 'Semaine dernière (' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y') . ')';
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $dateRangeText = 'Ce mois (' . $startDate->format('m/Y') . ')';
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $dateRangeText = 'Mois dernier (' . $startDate->format('m/Y') . ')';
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $dateRangeText = 'Cette année (' . $startDate->format('Y') . ')';
                break;
            case 'custom':
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
                $dateRangeText = 'Du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y');
                break;
        }

        return [$startDate, $endDate, $dateRangeText];
    }

    /**
     * Récupère les données en fonction du type de rapport
     */
    private function getReportData($reportType, $startDate, $endDate)
    {
        switch ($reportType) {
            case 'users':
                return User::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get();
            case 'events':
                return Event::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['category', 'organizer'])
                    ->orderBy('start_date', 'desc')
                    ->get();
            case 'orders':
                return Order::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['user', 'event'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            case 'payments':
                return Payment::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['order', 'order.user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            case 'custom_events':
                return CustomPersonalEvent::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['organizer', 'guests'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            case 'custom_offers':
                return CustomOfferPurchase::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            default:
                return collect();
        }
    }

    /**
     * Génère le titre du rapport
     */
    private function getReportTitle($reportType)
    {
        switch ($reportType) {
            case 'users':
                return 'Rapport des utilisateurs';
            case 'events':
                return 'Rapport des événements';
            case 'orders':
                return 'Rapport des reservations';
            case 'payments':
                return 'Rapport des paiements';
            case 'custom_events':
                return 'Rapport des événements personnalisés';
            case 'custom_offers':
                return 'Rapport des achats de formules personnalisées';
            default:
                return 'Rapport';
        }
    }

    /**
     * Génère un rapport PDF
     */
    private function generatePdfReport($type, $data, $title, $dateRange)
    {
        $pdf = PDF::loadView('dashboard.admin.reports.pdf', [
            'type' => $type,
            'data' => $data,
            'title' => $title,
            'dateRange' => $dateRange
        ]);

        return $pdf->download($this->getFileName($type, 'pdf'));
    }

    /**
     * Génère un rapport Excel
     */
    private function generateExcelReport($type, $data, $title, $dateRange)
    {
        switch ($type) {
            case 'users':
                return Excel::download(new UsersExport($data, $title, $dateRange), $this->getFileName($type, 'xlsx'));
            case 'events':
                return Excel::download(new EventsExport($data, $title, $dateRange), $this->getFileName($type, 'xlsx'));
            case 'orders':
                return Excel::download(new OrdersExport($data, $title, $dateRange), $this->getFileName($type, 'xlsx'));
            case 'payments':
                return Excel::download(new AdminPaymentsExport($data, $dateRange), $this->getFileName($type, 'xlsx'));
            default:
                // Fallback générique: exporter en CSV via Excel si disponible (colonnes à plat)
                return $this->generateCsvReport($type, $data, $title, $dateRange);
        }
    }

    /**
     * Génère un rapport CSV
     */
    private function generateCsvReport($type, $data, $title, $dateRange)
    {
        // Générique CSV: aplatit les attributs principaux et certaines relations communes
        $filename = $this->getFileName($type, 'csv');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($type, $data) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Déterminer colonnes selon type
            $columns = [];
            if ($type === 'custom_offers') {
                $columns = ['id','user_email','plan','price','operator','phone','used_at','created_at'];
                fputcsv($out, $columns);
                foreach ($data as $row) {
                    fputcsv($out, [
                        $row->id,
                        optional($row->user)->email,
                        $row->plan,
                        $row->price,
                        $row->operator,
                        $row->phone,
                        optional($row->used_at)?->format('Y-m-d H:i:s'),
                        optional($row->created_at)?->format('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($type === 'custom_events') {
                $columns = ['id','title','organizer_email','start_date','end_date','location','guests_count','created_at'];
                fputcsv($out, $columns);
                foreach ($data as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->title,
                        optional($row->organizer)->email,
                        optional($row->start_date)?->format('Y-m-d H:i:s'),
                        optional($row->end_date)?->format('Y-m-d H:i:s'),
                        $row->location,
                        $row->guests?->count() ?? 0,
                        optional($row->created_at)?->format('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                // Fallback simple pour autres types déjà couverts par Exports
                // Écrire titres dynamiques basiques
                if ($first = $data->first()) {
                    fputcsv($out, array_keys($first->getAttributes()));
                    foreach ($data as $row) {
                        fputcsv($out, array_values($row->getAttributes()));
                    }
                }
            }

            fclose($out);
        }, 200, $headers);
    }

    /**
     * Génère un nom de fichier pour le rapport
     */
    private function getFileName($type, $extension)
    {
        $date = Carbon::now()->format('Y-m-d');
        return "rapport_{$type}_{$date}.{$extension}";
    }
}


