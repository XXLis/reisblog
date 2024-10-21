
<?php
// Start de sessie om gebruikersgegevens op te slaan
session_start();
include 'config.php'; // Laad de databaseconfiguratie

// Controleer of het formulier is verzonden via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Bereid de SQL-query voor om de gebruiker op te zoeken op basis van e-mail
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Controleer of er een gebruiker met het ingevoerde e-mailadres bestaat
    if ($stmt->num_rows > 0) {
        // Gebruikersgegevens ophalen
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();

        // Controleer of het ingevoerde wachtwoord overeenkomt met het gehashte wachtwoord
        if (password_verify($password, $hashed_password)) {
            // Sla de gebruikersgegevens op in de sessie voor latere toegang
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Verwijs de gebruiker naar de hoofdpagina na een succesvolle inlogpoging
            header("Location: index.php");
            exit();
        } else {
            // Toon een algemene foutmelding bij een verkeerd wachtwoord
            $error = "Inloggegevens onjuist. Controleer uw e-mailadres en wachtwoord en probeer het opnieuw.";
        }
    } else {
        // Toon een algemene foutmelding als de gebruiker niet bestaat
        $error = "Inloggegevens onjuist. Controleer uw e-mailadres en wachtwoord en probeer het opnieuw.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Inloggen</h2>

        <!-- Toon de foutmelding als deze bestaat -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Inlogformulier -->
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Uw e-mailadres" required>
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Uw wachtwoord" required>
            </div>
            <button type="submit" class="btn btn-primary">Inloggen</button>
        </form>

        <!-- Wachtwoord vergeten link -->
        <p><a href="forgot_password.php">Wachtwoord vergeten?</a></p>
        <a href="index.php" class="btn btn-secondary mt-3">Terug</a>
    </div>

    <!-- Bootstrap en jQuery scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
