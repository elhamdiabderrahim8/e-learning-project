<?php
// Désactiver l'affichage des erreurs pour éviter de corrompre le flux binaire de l'image
ini_set('display_errors', 0);
error_reporting(0);

// Nettoyage impératif du tampon de sortie
ob_start();
if (ob_get_length()) ob_clean();

// 1. Paramètres
$nom_prof = isset($_GET['nom']) ? mb_strtoupper($_GET['nom'], 'UTF-8') : "PROFESSEUR";
$taille = 400;
$img = imagecreatetruecolor($taille, $taille);

// 2. Transparence totale (Arrière-plan détruit)
imagealphablending($img, false);
$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $transparent);
imagesavealpha($img, true);
imagealphablending($img, true);

// 3. Couleur Gold/Or
$gold = imagecolorallocate($img, 184, 134, 11); 
$centre = $taille / 2;

// 4. Dessin des cercles
imagesetthickness($img, 5);
imageellipse($img, $centre, $centre, 380, 380, $gold); 
imagesetthickness($img, 2);
imageellipse($img, $centre, $centre, 260, 260, $gold);

// 5. Texte "ENJAH" en haut
$texte_haut = "ENJAH";
$rayon = 150;
$angle_depart = 270 - (strlen($texte_haut) * 6); 
for ($i = 0; $i < strlen($texte_haut); $i++) {
    $angle = deg2rad($angle_depart + ($i * 12)); 
    $x = $centre + cos($angle) * $rayon;
    $y = $centre + sin($angle) * $rayon;
    imagechar($img, 5, (int)$x - 5, (int)$y - 5, $texte_haut[$i], $gold);
}

// 6. Nom au centre
$font = 5;
$largeur = strlen($nom_prof) * imagefontwidth($font);
imagestring($img, $font, (int)($centre - ($largeur/2)), (int)($centre - 10), $nom_prof, $gold);

// 7. Envoi du flux
header("Content-type: image/png");
// Empêcher la mise en cache pour voir les modifications immédiatement
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
imagepng($img);
imagedestroy($img);
exit;