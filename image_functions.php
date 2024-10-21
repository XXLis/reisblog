
<?php
// Functie om de afbeelding te comprimeren en te herschalen
function resizeAndCompressImage($source, $destination, $max_width, $max_height, $quality) {
    $info = getimagesize($source);
    $width = $info[0];
    $height = $info[1];

    // Ondersteunde afbeeldingstypen controleren
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false; // Ondersteund formaat niet gevonden
    }

    // Bereken de nieuwe breedte en hoogte, behoud van aspect ratio
    $aspect_ratio = $width / $height;
    if ($width > $max_width || $height > $max_height) {
        if ($width / $height > $aspect_ratio) {
            $new_width = $max_width;
            $new_height = $max_width / $aspect_ratio;
        } else {
            $new_width = $max_height * $aspect_ratio;
            $new_height = $max_height;
        }
    } else {
        // Als de afbeelding al kleiner is, geen herverkleining toepassen
        $new_width = $width;
        $new_height = $height;
    }

    // Maak een nieuwe afbeelding van het juiste formaat
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Behoud transparantie voor PNG- en GIF-afbeeldingen
    if ($info['mime'] == 'image/png' || $info['mime'] == 'image/gif') {
        imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }

    // Herschalen van de afbeelding naar de nieuwe afmetingen
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Opslaan van de gecomprimeerde en herschaalde afbeelding
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg($new_image, $destination, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        imagegif($new_image, $destination);
    } elseif ($info['mime'] == 'image/png') {
        imagepng($new_image, $destination, round(9 * (100 - $quality) / 100)); // Compressieniveau voor PNG
    }

    imagedestroy($image);
    imagedestroy($new_image);
}
