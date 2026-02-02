<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PDF;

class GenerateTicketsPDF extends Command
{
    protected $signature = 'tickets:generate-pdf {payment_id} {--all}';
    protected $description = 'Génère les PDFs des billets pour un paiement donné';

    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        $downloadAll = $this->option('all');

        $payment = Payment::with([
            'order.event.organizer',
            'order.tickets'
        ])->findOrFail($paymentId);

        $tempDir = storage_path('app/temp/tickets/' . $payment->id . '/');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $pdfFiles = [];
        $currentIndex = 0;
        $totalTickets = 0;

        foreach ($payment->order->tickets as $ticket) {
            $totalTickets += $ticket->pivot->quantity ?? 1;
        }

        foreach ($payment->order->tickets as $ticketData) {
            $quantity = $ticketData->pivot->quantity ?? 1;

            for ($i = 0; $i < $quantity; $i++) {
                $currentIndex++;

                $pdf = PDF::loadView('tickets.pdf', [
                    'event' => $payment->order->event,
                    'ticket' => $ticketData,
                    'payment' => $payment,
                    'index' => $currentIndex,
                    'total' => $totalTickets,
                    'qrCode' => $this->generateQRCode($payment->id . '-' . $currentIndex)
                ]);

                // Configuration spécifique pour le format paysage
                $pdf->setPaper([0, 0, 70, 180], 'landscape');
                $pdf->setOption('margin-top', 0);
                $pdf->setOption('margin-right', 0);
                $pdf->setOption('margin-bottom', 0);
                $pdf->setOption('margin-left', 0);
                $pdf->setOption('encoding', 'UTF-8');

                $tempFile = $tempDir . 'ticket_' . $currentIndex . '.pdf';
                $pdf->save($tempFile);
                $pdfFiles[] = $tempFile;
            }
        }

        if (count($pdfFiles) > 1) {
            $merger = new \Jurosh\PDFMerge\PDFMerger;
            foreach ($pdfFiles as $file) {
                $merger->addPDF($file, 'all');
            }

            $outputPath = $tempDir . 'ticket.pdf';
            $merger->merge();
            $merger->save($outputPath);

            $mergedContent = File::get($outputPath);
            Storage::put('tickets/' . $payment->id . '/ticket.pdf', $mergedContent);
        } else {
            $content = File::get($pdfFiles[0]);
            Storage::put('tickets/' . $payment->id . '/ticket.pdf', $content);
        }

        // Nettoyage
        File::deleteDirectory($tempDir);

        $this->info('PDFs générés avec succès');
    }

    private function generateQRCode($data)
    {
        $renderer = new \BaconQrCode\Renderer\Image\SvgImageBackEnd();
        $rendererStyle = new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400);
        $writer = new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer($rendererStyle, $renderer));

        return 'data:image/svg+xml;base64,' . base64_encode($writer->writeString($data));
    }
}
