<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateDefaultImages extends Command
{
    protected $signature = 'app:create-default-images';
    protected $description = 'Crée les images par défaut pour l\'application';

    public function handle()
    {
        $this->info('Création des images par défaut...');

        // Vérifier si le dossier images existe
        $imagesPath = public_path('images');
        if (!File::exists($imagesPath)) {
            $this->info("Le dossier images n'existe pas. Création...");
            File::makeDirectory($imagesPath, 0755, true);
            $this->info("Dossier images créé avec succès.");
        }

        // Créer une bannière par défaut si elle n'existe pas
        $defaultBannerPath = public_path('images/default-banner.jpg');
        if (!File::exists($defaultBannerPath)) {
            $this->info("La bannière par défaut n'existe pas. Création...");
            
            // Créer une image simple
            $width = 1200;
            $height = 300;
            $image = imagecreatetruecolor($width, $height);
            
            // Définir un dégradé de couleur
            $startColor = imagecolorallocate($image, 41, 128, 185); // Bleu
            $endColor = imagecolorallocate($image, 52, 152, 219); // Bleu clair
            
            // Remplir l'image avec un dégradé
            for ($i = 0; $i < $width; $i++) {
                $color = imagecolorallocate(
                    $image,
                    41 + ($i / $width) * (52 - 41),
                    128 + ($i / $width) * (152 - 128),
                    185 + ($i / $width) * (219 - 185)
                );
                imageline($image, $i, 0, $i, $height, $color);
            }
            
            // Ajouter du texte
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $font = 5; // Taille de police intégrée
            $text = "MokiliEvent";
            
            // Centrer le texte
            $textWidth = imagefontwidth($font) * strlen($text);
            $textHeight = imagefontheight($font);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            
            imagestring($image, $font, $x, $y, $text, $textColor);
            
            // Sauvegarder l'image
            imagejpeg($image, $defaultBannerPath, 90);
            imagedestroy($image);
            
            $this->info("Bannière par défaut créée avec succès.");
        } else {
            $this->info("La bannière par défaut existe déjà.");
        }

        $this->info('Terminé !');
        
        return Command::SUCCESS;
    }
}