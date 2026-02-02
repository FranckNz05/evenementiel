<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageOptimizationService
{
    private const QUALITY = 85;
    private const WEBP_QUALITY = 80;
    private const THUMBNAIL_SIZES = [
        'small' => [150, 150],
        'medium' => [300, 300],
        'large' => [600, 600],
        'xl' => [1200, 1200]
    ];
    
    /**
     * Optimise une image et génère les différentes tailles
     */
    public function optimizeImage(string $imagePath, string $disk = 'public'): array
    {
        try {
            $fullPath = Storage::disk($disk)->path($imagePath);
            $pathInfo = pathinfo($imagePath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'];
            
            $optimizedImages = [];
            
            // Optimiser l'image originale
            $originalOptimized = $this->optimizeOriginalImage($fullPath, $directory, $filename, $extension, $disk);
            if ($originalOptimized) {
                $optimizedImages['original'] = $originalOptimized;
            }
            
            // Générer les thumbnails
            foreach (self::THUMBNAIL_SIZES as $size => $dimensions) {
                $thumbnail = $this->generateThumbnail($fullPath, $directory, $filename, $extension, $dimensions, $disk);
                if ($thumbnail) {
                    $optimizedImages[$size] = $thumbnail;
                }
            }
            
            // Générer la version WebP
            $webpVersion = $this->generateWebP($fullPath, $directory, $filename, $disk);
            if ($webpVersion) {
                $optimizedImages['webp'] = $webpVersion;
            }
            
            return $optimizedImages;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'optimisation de l\'image: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Optimise l'image originale
     */
    private function optimizeOriginalImage(string $fullPath, string $directory, string $filename, string $extension, string $disk): ?string
    {
        try {
            $image = Image::make($fullPath);
            
            // Redimensionner si trop grande
            if ($image->width() > 1920 || $image->height() > 1920) {
                $image->resize(1920, 1920, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Optimiser la qualité
            $optimizedPath = $directory . '/' . $filename . '_optimized.' . $extension;
            $image->save(Storage::disk($disk)->path($optimizedPath), self::QUALITY);
            
            return $optimizedPath;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'optimisation de l\'image originale: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Génère un thumbnail
     */
    private function generateThumbnail(string $fullPath, string $directory, string $filename, string $extension, array $dimensions, string $disk): ?string
    {
        try {
            $image = Image::make($fullPath);
            $image->fit($dimensions[0], $dimensions[1]);
            
            $thumbnailPath = $directory . '/' . $filename . '_' . $dimensions[0] . 'x' . $dimensions[1] . '.' . $extension;
            $image->save(Storage::disk($disk)->path($thumbnailPath), self::QUALITY);
            
            return $thumbnailPath;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du thumbnail: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Génère la version WebP
     */
    private function generateWebP(string $fullPath, string $directory, string $filename, string $disk): ?string
    {
        try {
            $image = Image::make($fullPath);
            
            // Redimensionner si trop grande
            if ($image->width() > 1920 || $image->height() > 1920) {
                $image->resize(1920, 1920, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            $webpPath = $directory . '/' . $filename . '.webp';
            $image->encode('webp', self::WEBP_QUALITY)->save(Storage::disk($disk)->path($webpPath));
            
            return $webpPath;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération WebP: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Génère le HTML optimisé pour l'image
     */
    public function generateOptimizedImageHtml(string $imagePath, string $alt = '', array $attributes = []): string
    {
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        
        // Chemin vers l'image WebP
        $webpPath = $directory . '/' . $filename . '.webp';
        
        // Chemin vers l'image originale optimisée
        $originalPath = $directory . '/' . $filename . '_optimized.' . $pathInfo['extension'];
        
        // Attributs par défaut
        $defaultAttributes = [
            'class' => 'img-fluid lazy',
            'loading' => 'lazy',
            'alt' => $alt,
            'width' => 'auto',
            'height' => 'auto'
        ];
        
        $attributes = array_merge($defaultAttributes, $attributes);
        
        // Générer le HTML
        $html = '<picture>';
        
        // Source WebP
        if (Storage::disk('public')->exists($webpPath)) {
            $html .= '<source srcset="' . asset('storage/' . $webpPath) . '" type="image/webp">';
        }
        
        // Source originale
        $html .= '<img src="' . asset('storage/' . $originalPath) . '"';
        
        foreach ($attributes as $key => $value) {
            $html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        $html .= '>';
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Nettoie les anciennes images optimisées
     */
    public function cleanupOldImages(string $imagePath, string $disk = 'public'): void
    {
        try {
            $pathInfo = pathinfo($imagePath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'];
            
            // Supprimer l'ancienne image optimisée
            $oldOptimized = $directory . '/' . $filename . '_optimized.' . $extension;
            if (Storage::disk($disk)->exists($oldOptimized)) {
                Storage::disk($disk)->delete($oldOptimized);
            }
            
            // Supprimer les anciens thumbnails
            foreach (self::THUMBNAIL_SIZES as $size => $dimensions) {
                $oldThumbnail = $directory . '/' . $filename . '_' . $dimensions[0] . 'x' . $dimensions[1] . '.' . $extension;
                if (Storage::disk($disk)->exists($oldThumbnail)) {
                    Storage::disk($disk)->delete($oldThumbnail);
                }
            }
            
            // Supprimer l'ancienne version WebP
            $oldWebp = $directory . '/' . $filename . '.webp';
            if (Storage::disk($disk)->exists($oldWebp)) {
                Storage::disk($disk)->delete($oldWebp);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage des anciennes images: ' . $e->getMessage());
        }
    }
}
