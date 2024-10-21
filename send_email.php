
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Autoload PHPMailer

function stuur_reset_email($gebruiker_email, $reset_link) {
    $mail = new PHPMailer(true);

    try {
        // Mailjet SMTP-configuratie
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com'; // SMTP-adres van Mailjet
        $mail->SMTPAuth   = true;
        $mail->Username   = 'be76513b55ce568a9687246f07ab6571'; // API Key van Mailjet
        $mail->Password   = '9f6826dde5ab6e8728aa3b7a5bf791b8'; // API Key van Mailjet
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;
        $mail->SMTPDebug = 2;


        // Afzender en ontvanger
        $mail->setFrom('reisblogplatform@gmail.com', 'Reisblog'); // Afzenderadres
        $mail->addAddress($gebruiker_email); // Ontvangeradres (gebruiker)

        // Berichtinhoud
        $mail->isHTML(true);
        $mail->Subject = 'Wachtwoord reset';
        $mail->Body    = "Klik op deze link om je wachtwoord te resetten: <a href='$reset_link'>$reset_link</a>";

        // E-mail verzenden
        $mail->send();
        echo "E-mail is verzonden.";
    } catch (Exception $e) {
        echo "E-mail kon niet verzonden worden. Fout: {$mail->ErrorInfo}";
    }
}
?>
