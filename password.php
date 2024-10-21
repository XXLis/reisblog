
<?php
$haslo = 'admin'; // Vul hier je nieuwe wachtwoord in
$hashed_password = password_hash($haslo, PASSWORD_DEFAULT);
echo $hashed_password;
?>
