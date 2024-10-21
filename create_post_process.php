
<?php
// Start de sessie
session_start();

// Voeg het configuratiebestand met de databaseverbinding toe
include 'config.php';
include 'image_functions.php'; // Voegt je compressie en herschalen functie toe

// Controleren of het formulier is ingediend via een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controleren of de gebruiker is ingelogd
    if (!isset($_SESSION['user_id'])) {
        die('Je bent niet ingelogd.');
    }

    // Gegevens uit het formulier ophalen
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Nieuwe post in de database invoegen
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('ssi', $title, $content, $user_id);

    // Controleren of de query is uitgevoerd
    if (!$stmt->execute()) {
        die('Fout bij het toevoegen van de post: ' . $conn->error);
    }

    // ID van de nieuw toegevoegde post ophalen
    $post_id = $stmt->insert_id;

    // Controleren en uploaden van afbeeldingen als deze aanwezig zijn
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/';
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            // Uploaden van het bestand
            if (move_uploaded_file($tmp_name, $targetFilePath)) {
                // Afbeelding herschalen en comprimeren naar 640x480px met 75% kwaliteit
                resizeAndCompressImage($targetFilePath, $targetFilePath, 640, 480, 75);

                // Controleren of de afbeelding na compressie kleiner is dan 2MB
                if (filesize($targetFilePath) > 2097152) { // 2MB limiet
                    echo "Het bestand $fileName is te groot na compressie.";
                    unlink($targetFilePath); // Verwijderen als het bestand te groot is
                } else {
                    // Afbeeldingspad in de database invoegen
                    $stmt_image = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
                    $stmt_image->bind_param('is', $post_id, $targetFilePath);
                    $stmt_image->execute();
                }
            } else {
                echo "Er was een probleem bij het uploaden van het bestand $fileName.";
            }
        }
    }

    // Doorverwijzen na succesvolle invoer
    header("Location: index.php");
    exit();
} else {
    echo "Ongeldige verzoekmethode.";
}
