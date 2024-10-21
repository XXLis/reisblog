
<?php
// Start de sessie
session_start();
include 'config.php'; // Laad de databaseconfiguratie
include 'image_functions.php'; // Laad de functie voor afbeeldingcompressie

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Haal het post-ID op uit de URL en valideer
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die('Geen geldige post geselecteerd');
}

$post_id = (int)$_GET['id'];

// Haal de postgegevens op uit de database
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();

if ($post_result->num_rows === 0) {
    die('Post niet gevonden');
}

$post = $post_result->fetch_assoc();

// Controleer of de ingelogde gebruiker eigenaar van de post is of admin
if ($_SESSION['user_id'] !== $post['user_id'] && $_SESSION['role'] !== 'admin') {
    die('Geen toegang tot deze post');
}

// Haal de afbeeldingen op die bij de post horen
$stmt = $conn->prepare("SELECT * FROM post_images WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$images_result = $stmt->get_result();

// Verwerk het formulier wanneer het wordt ingediend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de invoer van de gebruiker op en trim witte ruimte
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Valideer invoer
    if (!empty($title) && !empty($content)) {
        // Update de post in de database
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $post_id);

        if ($stmt->execute()) {
            // Verwerk nieuwe afbeeldingen
            if (!empty($_FILES['new_images']['name'][0])) {
                $upload_dir = 'uploads/';
                foreach ($_FILES['new_images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = basename($_FILES['new_images']['name'][$key]);
                    $target_file = $upload_dir . uniqid() . "_" . $file_name;

                    // Verplaats het geüploade bestand naar de uploads map
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        // Comprimeer de afbeelding na uploaden (640x480 pixels, 75% kwaliteit)
                        resizeAndCompressImage($target_file, $target_file, 640, 480, 75);

                        // Controleren of de afbeelding na compressie kleiner is dan 2MB
                        if (filesize($target_file) > 2097152) { // 2MB limiet
                            echo "Het bestand $file_name is te groot na compressie.";
                            unlink($target_file); // Verwijder als het bestand te groot is
                        } else {
                            // Sla de afbeelding op in de database
                            $stmt = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
                            $stmt->bind_param("is", $post_id, $target_file);
                            $stmt->execute();
                        }
                    } else {
                        echo "Fout bij het uploaden van de afbeelding: " . $file_name;
                    }
                }
            }

            // Verwijs de gebruiker door naar de homepage na succesvolle update
            header("Location: index.php");
            exit();
        } else {
            echo "Fout bij het updaten van de post.";
        }
    } else {
        echo "Alle velden moeten worden ingevuld.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Bewerken</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Post Bewerken</h2>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titel</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Inhoud</label>
                <textarea name="content" id="content" class="form-control" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Huidige Afbeeldingen:</label>
                <div class="row">
                    <?php while ($image = $images_result->fetch_assoc()): ?>
                        <div class="col-md-3 text-center">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Post Image" class="img-thumbnail mb-2" style="max-height: 150px;">
                            <br>
                            <a href="delete_image.php?id=<?php echo $image['id']; ?>&post_id=<?php echo $post_id; ?>" class="btn btn-danger btn-sm">Verwijderen</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="new_images">Nieuwe Afbeeldingen Toevoegen:</label>
                <input type="file" name="new_images[]" id="new_images" class="form-control-file" multiple>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a href="index.php" class="btn btn-secondary">Annuleren</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
