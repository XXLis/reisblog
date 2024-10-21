<?php
session_start();
include 'config.php'; // Database configuratie

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ontvang de ingevoerde gegevens
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash het wachtwoord voor veiligheid

    // Controleer of de gebruikersnaam of e-mail al bestaat in de database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Gebruikersnaam of e-mail bestaat al
        $error = "De gebruikersnaam of het e-mailadres is al in gebruik. Probeer een andere.";
    } else {
        // Voeg de gebruiker toe aan de database als de gebruikersnaam en e-mail uniek zijn
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            // Zet de succesboodschap in een sessie en gebruik PRG pattern
            $_SESSION['success'] = "Registratie succesvol! <a href='login.php'>Inloggen</a>";

            // PRG pattern: doorverwijzen na succesvolle registratie
            header("Location: register.php");
            exit();
        } else {
            $error = "Er is iets fout gegaan tijdens de registratie. Probeer het opnieuw.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Registreren</h2>
        
        <!-- Foutmeldingen tonen -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Succesbericht tonen als het bestaat in de sessie -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']); // Verwijder succesmelding na weergave
                ?>
            </div>
        <?php endif; ?>

        <!-- Registratieformulier -->
        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Gebruikersnaam</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Wachtwoord</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registreren</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Terug</a>
    </div>
</body>
</html>
