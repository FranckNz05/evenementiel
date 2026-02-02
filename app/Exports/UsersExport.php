<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;

class UsersExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $data;
    protected $title;
    protected $dateRange;

    public function __construct($data, $title, $dateRange)
    {
        $this->data = $data;
        $this->title = $title;
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        return $this->data->map(function ($user) {
            return [
                'Nom complet' => trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),
                'Email' => $user->email,
                'Téléphone' => $user->phone ?? '—',
                'Date d\'inscription' => $user->created_at->format('d/m/Y'),
                'Statut' => $user->email_verified_at ? 'Vérifié' : 'Non vérifié'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nom complet',
            'Email',
            'Téléphone',
            'Date d\'inscription',
            'Statut'
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function title(): string
    {
        return 'Utilisateurs';
    }

    public function styles(Worksheet $sheet)
    {
        $siteName = DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent';
        
        // En-tête avec logo et titre
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $siteName);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setARGB('FF0F1A3D');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', $this->title);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF1A237E');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('2')->setRowHeight(25);

        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', 'Période : ' . $this->dateRange);
        $sheet->getStyle('A3')->getFont()->setSize(11)->getColor()->setARGB('FF6B7280');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('3')->setRowHeight(20);

        // Style pour les en-têtes de colonnes (ligne 4)
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0F1A3D');
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('4')->setRowHeight(25);

        // Bordures pour les en-têtes
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0A1229'));

        // Bordures pour toutes les cellules de données
        $lastRow = $this->data->count() + 4;
        $dataRange = 'A4:E' . $lastRow;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFE5E7EB'));

        // Alternance de couleurs pour les lignes
        for ($row = 5; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF9FAFB');
            }
        }

        // Alignement des colonnes
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B4:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D4:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E4:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);

        return [];
    }
}
