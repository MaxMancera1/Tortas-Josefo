<?php
session_start();
include_once __DIR__ . '/models/usuario/usuario.class.php';
$app = new Usuario();
$accion = $_GET['accion'] ?? 'login';
$error = '';
switch ($accion) {
    case 'logout':
        session_destroy();
        header('Location: login.php');
        exit;
    default:
        if (isset($_POST['enviar'])) {
            $usuario = $app->login($_POST['correo'], $_POST['password']);
            if ($usuario) {
                session_regenerate_id(true);
                $_SESSION['usuario'] = $usuario['nombre'];
                header('Location: proveedor.php');
                exit;
            }
            $error = 'Correo o contraseña incorrectos';
        }
        include_once __DIR__ . '/../templates/usuario/login.html';
}
