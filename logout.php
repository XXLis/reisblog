
<?php
// Start of continueer de sessie
session_start();

// Vernietig alle sessiegegevens
session_destroy();

// Redirect naar de hoofdpagina
header("Location: index.php");
exit();
?>
