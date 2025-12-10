<?php
session_start();
session_unset(); // Libera todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirige al index principal
header("Location: index.php");
exit;
?>