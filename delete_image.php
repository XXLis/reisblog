
<?php
// Start sessie
session_start();
include 'config.php'; // Databaseconfiguratie

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Controleer of het afbeelding-ID is opgegeven
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die('Ongeldig verzoek');
}

$image_id = (int)$_GET['id'];
$post_id = (int)$_GET['post_id'];

// Haal de afbeelding op uit de database
$stmt = $conn->prepare("SELECT image_path FROM post_images WHERE id = ?");
$stmt->bind_param("i", $image_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Afbeelding niet gevonden');
}

$image = $result->fetch_assoc();
$image_path = $image['image_path'];

// Verwijder de afbeelding uit de database
$stmt = $conn->prepare("DELETE FROM post_images WHERE id = ?");
$stmt->bind_param("i", $image_id);

if ($stmt->execute()) {
    // Verwijder de afbeelding van de server
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Stuur de gebruiker terug naar de bewerkingspagina van de post
    header("Location: edit_post.php?id=" . $post_id);
    exit();
} else {
    echo "Fout bij het verwijderen van de afbeelding.";
}
?>
