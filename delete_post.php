
<?php
// Start sessie
session_start();
include 'config.php'; // Database configuratie

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Haal de post-ID op uit de URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die('Geen post geselecteerd');
}

$post_id = (int)$_GET['id'];

// Haal postgegevens op uit de database
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Post niet gevonden');
}

$post = $result->fetch_assoc();

// Controleer of de ingelogde gebruiker eigenaar is van de post of admin is
if ($_SESSION['user_id'] !== $post['user_id'] && $_SESSION['role'] !== 'admin') {
    die('Geen toegang tot deze post');
}

// Verwijder post en bijbehorende afbeeldingen
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    // Verwijder afbeeldingen
    $stmt_img = $conn->prepare("SELECT image_path FROM post_images WHERE post_id = ?");
    $stmt_img->bind_param("i", $post_id);
    $stmt_img->execute();
    $result_images = $stmt_img->get_result();

    while ($row = $result_images->fetch_assoc()) {
        $image_path = $row['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path); // Verwijder afbeelding van de server
        }
    }

    // Verwijder alle afbeelding records uit de database
    $stmt_img_del = $conn->prepare("DELETE FROM post_images WHERE post_id = ?");
    $stmt_img_del->bind_param("i", $post_id);
    $stmt_img_del->execute();

    // Redirect naar de homepage
    header("Location: index.php");
    exit();
} else {
    echo "Fout bij het verwijderen van de post.";
}
?>
