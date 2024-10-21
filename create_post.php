
<?php
// Start de sessie
session_start();

// Controleren of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Post Aanmaken</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Bootstrap CSS toevoegen -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Nieuwe Post Aanmaken</h2>

        <!-- Formulier om een nieuwe post aan te maken -->
        <form action="create_post_process.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titel</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="content">Inhoud</label>
                <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
            </div>

            <!-- Afbeeldingen - alleen JPG, PNG, en GIF toestaan -->
            <div class="form-group">
                <label for="images">Afbeeldingen (alleen GIF, JPG, PNG toegestaan)</label>
                <input type="file" name="images[]" id="images" class="form-control-file" multiple accept=".jpg,.jpeg,.png,.gif" required>
            </div>

            <button type="submit" class="btn btn-primary">Post Aanmaken</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
