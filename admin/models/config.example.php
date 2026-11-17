<?php
// Copia este archivo a config.php y pon ahí tus datos reales.
// config.php no se sube a git.

define('DB_HOST', 'mariadb');
define('DB_USER', 'user');
define('DB_PASS', 'password');
define('DB_NAME', 'tortas');
define('DB_PORT', '3306');
define('DB_DRIVER', 'mysql');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-correo@gmail.com');
define('SMTP_PASS', 'tu-contrasena-de-aplicacion');
define('SMTP_FROM', 'tu-correo@gmail.com');
define('SMTP_FROM_NAME', 'Tortas Josefo');
define('MENU_QR_URL', 'https://tortas-josefo.netlify.app/');

// Client ID de la app Sandbox: https://developer.paypal.com/dashboard/applications/sandbox
define('PAYPAL_CLIENT_ID', 'PON_AQUI_EL_CLIENT_ID');
?>
