<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminPaymentsExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles, ShouldAutoSize, WithEvents
{
    protected Collection $payments;
    protected string $periodLabel;

    public function __construct(Collection $payments, string $periodLabel = '')
    {
        $this->payments = $payments;
        $this->periodLabel = $periodLabel ?: 'Tous les paiements';
    }

    public function collection()
    {
        return $this->payments->map(function ($payment) {
            return [
                'Référence' => $payment->reference_transaction ?? $payment->matricule ?? 'N/A',
                'Date' => optional($payment->created_at)->format('d/m/Y H:i'),
                'Client' => optional($payment->user)->prenom . ' ' . optional($payment->user)->nom,
                'Email' => optional($payment->user)->email,
                'Événement' => optional($payment->event)->title,
                'Montant (FCFA)' => $payment->montant,
                'Mode' => $payment->methode_paiement,
                'Statut' => ucfirst($payment->statut),
                'Téléphone' => $payment->numero_telephone ?? '—',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date',
            'Client',
            'Email',
            'Événement',
            'Montant (FCFA)',
            'Mode',
            'Statut',
            'Téléphone',
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function styles(Worksheet $sheet)
    {
        // Style de la ligne d'en-têtes (row 4)
        $sheet->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A4:I4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:I4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF0F172A');

        // Format monétaire pour la colonne montant
        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');

        return [
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');

                $sheet->setCellValue('A1', 'Rapport des paiements');
                $sheet->setCellValue('A2', 'Période : ' . $this->periodLabel);

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF0F172A');
                $sheet->getStyle('A2')->getFont()->setSize(11)->getColor()->setARGB('FF6B7280');

                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}

