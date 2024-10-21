
<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Laad de autoloader voor PHPMailer
require 'config.php'; // Databaseverbinding

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {

    $user_email = $_POST['email'];

    // Controleer of het e-mailadres in de database bestaat
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Gebruiker bestaat, genereer een token
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // Genereer een token
        $reset_link = "https://reisblogplatform.byethost7.com/reset_password.php?token=$token"; // Wachtwoord reset link

        // Sla de token op in de password_resets tabel, gebruik `email` in plaats van `user_id`
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_email, $token);
        $stmt->execute();

        // Configuratie voor Mailjet
        $mail = new PHPMailer(true);
        try {
            // Debug-instellingen
            $mail->SMTPDebug = 0; // Schakel debug-informatie uit: 0 = geen, 2 = gedetailleerde info over SMTP
            $mail->isSMTP();
            $mail->Host = 'in-v3.mailjet.com'; // Mailjet SMTP-server
            $mail->SMTPAuth = true;
            $mail->Username = 'f5833018091fe6914f8309e381618193'; // API Key Mailjet
            $mail->Password = '72f0dc93930f17d5df85e5c8d483aad9'; // Secret Key Mailjet
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Beveiliging via TLS
            $mail->Port = 587;

            // Instellingen voor afzender en ontvanger
            $mail->setFrom('reisblogplatform@gmail.com', 'Reisblog Platform'); // Verifieer of dit e-mailadres geldig en geverifieerd is
            $mail->addAddress($user_email); // Ontvanger's e-mailadres

            // SSL/TLS-beveiliging - Schakel verificatie van certificaten uit
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Inhoud van de e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Reset wachtwoord';
            $mail->Body = "Klik op deze link om uw wachtwoord te resetten: <a href='$reset_link'>$reset_link</a>";

            // Verstuur de e-mail
            if ($mail->send()) {
                echo 'De reset link is naar uw e-mailadres verzonden!';
            } else {
                echo 'Er is een probleem opgetreden bij het verzenden van de e-mail.';
            }
        } catch (Exception $e) {
            echo "De e-mail kon niet verstuurd worden. Foutmelding: {$mail->ErrorInfo}";
        }
    } else {
        echo "Het opgegeven e-mailadres is niet geregistreerd.";
    }
} else {
    // Toon het formulier als er geen POST is verstuurd
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Wachtwoord vergeten</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h2>Wachtwoord vergeten</h2>
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label>Voer uw e-mailadres in</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Verstuur reset link</button>
            </form>
        </div>
    </body>
    </html>
<?php
}
?>
