
<?php
// Start de sessie
session_start();
include 'config.php'; // Voeg het configuratiebestand met de databaseverbinding toe

// Controleren of de databaseverbinding is gemaakt
if (!$conn) {
    die("Verbindingsfout: " . mysqli_connect_error());
}

// Ophalen van posts en gebruikersinformatie
$stmt = $conn->prepare("SELECT posts.id, posts.title, posts.content, posts.user_id, posts.created_at, users.username 
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC");

if (!$stmt->execute()) {
    die('Fout bij het uitvoeren van de query: ' . $conn->error);
}
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reisblog Platform</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Bootstrap CSS toevoegen -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Reisblog Platform</h2>

        <!-- Controleren of de gebruiker is ingelogd -->
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welkom, <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a> | <a href="logout.php">Uitloggen</a></p>
            
            <!-- Verschillende knoppen tonen afhankelijk van de rol van de gebruiker -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="create_post.php" class="btn btn-primary">Nieuwe Post (Admin)</a>
            <?php else: ?>
                <a href="create_post.php" class="btn btn-primary">Nieuwe Post</a>
            <?php endif; ?>
        
        <!-- Link naar inloggen of registreren tonen als de gebruiker niet is ingelogd -->
        <?php else: ?>
            <p><a href="login.php">Inloggen</a> of <a href="register.php">Registreren</a></p>
        <?php endif; ?>

        <hr>

        <!-- Controleren of er posts zijn om weer te geven -->
        <?php if ($result->num_rows > 0): ?>
            <!-- Posts weergeven -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                    <!-- Afbeeldingen voor de post ophalen -->
                    <?php
                    $post_id = $row['id'];
                    $stmt_images = $conn->prepare("SELECT image_path FROM post_images WHERE post_id = ?");
                    $stmt_images->bind_param("i", $post_id);
                    $stmt_images->execute();
                    $result_images = $stmt_images->get_result();
                    if ($result_images->num_rows > 0): ?>
                        <div class="post-images row">
                            <!-- Elke afbeelding tonen -->
                            <?php while ($image_row = $result_images->fetch_assoc()): ?>
                                <div class="col-12 col-sm-6 mb-3">
                                    <img src="<?php echo htmlspecialchars($image_row['image_path']); ?>" alt="Afbeelding van de post" class="img-fluid clickable-image">
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Weergeven wie de post heeft geplaatst en wanneer -->
                    <small>Gepost door <?php echo htmlspecialchars($row['username']); ?> op <?php echo $row['created_at']; ?></small>

                    <!-- Bewerken en verwijderen knoppen tonen voor admins of auteurs van de post -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' || isset($_SESSION['user_id']) && $_SESSION['user_id'] === $row['user_id']): ?>
                        <div class="mt-2">
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Bewerken</a>
                            <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Verwijderen</a>
                        </div>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Er zijn nog geen posts om weer te geven.</p>
        <?php endif; ?>
    </div>

    <!-- Modal voor het vergroten van afbeeldingen -->
<div id="imageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Afbeelding</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Gebruik een dummy-waarde voor het src attribuut -->
                <img id="modal-image" src="#" class="img-fluid" alt="Afbeelding">
                <!-- Navigatieknoppen -->
                <button id="prevImage" class="btn btn-primary mt-3">Vorige</button>
                <button id="nextImage" class="btn btn-primary mt-3">Volgende</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>
