<?php
session_start();
include_once __DIR__ . '/models/empleado/empleado.class.php';
$app = new Empleado();
$accion = $_GET['accion'] ?? 'login';
$error = '';
switch ($accion) {
    case 'logout':
        session_destroy();
        header('Location: login.php');
        exit;
    default:
        if (isset($_POST['enviar'])) {
            $empleado = $app->login($_POST['correo'], $_POST['password']);
            if ($empleado) {
                session_regenerate_id(true);
                $_SESSION['id_empleado'] = $empleado['id'];
                $_SESSION['usuario'] = $empleado['nombre'];
                header('Location: index.php');
                exit;
            }
            $error = 'Correo o contraseña incorrectos';
        }
        include_once __DIR__ . '/../templates/admin/login.html';
}
