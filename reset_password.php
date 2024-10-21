
<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoloaden
require 'config.php'; // Databaseverbinding

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Controleeren of de token bestaat in de database
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Gebruiker heeft een nieuw wachtwoord ingevoerd
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Verkrijg het e-mailadres gekoppeld aan de token
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Update het wachtwoord van de gebruiker in de database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();

            // Verwijder de gebruikte token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            // Toon succesmelding en knop naar loginpagina
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h2>Uw wachtwoord is succesvol bijgewerkt.</h2>
                    <a href='login.php' class='btn btn-primary'>Ga naar inloggen</a>
                  </div>";
            exit;
        }
    } else {
        echo "Ongeldige token.";
    }
} else {
    echo "Geen token opgegeven.";
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord Resetten</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Reset uw wachtwoord</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nieuw wachtwoord:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Wachtwoord resetten</button>
        </form>
    </div>
</body>
</html>
