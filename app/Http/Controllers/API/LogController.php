<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Get all logs from laravel.log file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Chemin du fichier de log
            $logPath = storage_path('logs/laravel.log');
                                                                                                                                                                   
            // Vérifier si le fichier existe
            if (!File::exists($logPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier de log n\'existe pas',
                    'logs' => []
                ], 404);
            }

            // Paramètres de pagination
            $limit = (int) $request->get('limit', 1000); // Par défaut, 1000 dernières lignes
            $limit = min($limit, 10000); // Maximum 10000 lignes pour éviter les problèmes de mémoire
            $offset = (int) $request->get('offset', 0);
            $reverse = $request->get('reverse', 'true') === 'true'; // Par défaut, les dernières lignes en premier

            // Lire le fichier de manière efficace
            // Si on veut les dernières lignes, lire seulement la fin du fichier
            $fileSize = filesize($logPath);
            $readSize = $reverse ? min($fileSize, 5 * 1024 * 1024) : $fileSize; // Lire max 5MB si on veut les dernières lignes
            
            $handle = fopen($logPath, 'r');
            if (!$handle) {
                throw new \Exception('Impossible d\'ouvrir le fichier de log');
            }

            if ($reverse && $fileSize > $readSize) {
                // Se positionner à la fin du fichier moins la taille à lire
                fseek($handle, -$readSize, SEEK_END);
                // Lire et ignorer la première ligne (probablement incomplète)
                fgets($handle);
            }

            $logsArray = [];
            $lineCount = 0;
            
            // Lire ligne par ligne pour économiser la mémoire
            while (($line = fgets($handle)) !== false) {
                // Nettoyer les caractères UTF-8 mal formés
                $line = @iconv('UTF-8', 'UTF-8//IGNORE', $line);
                if ($line === false) {
                    $line = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $line);
                    $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                }
                
                $line = trim($line);
                if (!empty($line)) {
                    $logsArray[] = $line;
                    $lineCount++;
                }
            }
            
            fclose($handle);

            // Si reverse, prendre les dernières lignes
            if ($reverse) {
                $logsArray = array_slice($logsArray, -$limit);
            } else {
                // Appliquer offset et limit
                $logsArray = array_slice($logsArray, $offset, $limit);
            }

            // Compter le nombre total de lignes (approximatif pour les gros fichiers)
            $totalLines = $lineCount;
            if ($reverse && $fileSize > $readSize) {
                // Estimation basée sur la taille du fichier
                $avgLineLength = $fileSize / max($lineCount, 1);
                $totalLines = (int) ($fileSize / max($avgLineLength, 100));
            }

            return response()->json([
                'success' => true,
                'message' => 'Logs récupérés avec succès',
                'total_lines' => $totalLines,
                'returned_lines' => count($logsArray),
                'limit' => $limit,
                'offset' => $offset,
                'reverse' => $reverse,
                'logs' => array_values($logsArray)
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la lecture des logs: ' . $e->getMessage(),
                'logs' => []
            ], 500);
        }
    }
}

