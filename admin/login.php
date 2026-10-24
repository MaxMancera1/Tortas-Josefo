<?php
session_start();
include_once __DIR__ . '/models/empleado/empleado.class.php';
$app = new Empleado();
$accion = $_GET['accion'] ?? 'login';
$error = '';
$alerta = [];
$token = $_GET['token'] ?? '';
$formulario = null;

function urlRecuperacion($token){
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $base = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8081');
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin')), '/');
    return $base . $dir . '/login.php?accion=nueva&token=' . $token;
}

switch ($accion) {
    case 'logout':
        session_destroy();
        header('Location: login.php');
        exit;
    case 'recuperar_contrasena':
        $formulario = __DIR__ . '/../templates/admin/_form_correo.html';
        break;
    case 'enviar_correo':
        if (isset($_POST['enviar'])) {
            $correo = $_POST['correo'] ?? '';
            if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                // mismo armado de 64 caracteres que en clase: md5(...) . md5(...)
                $token = md5(bin2hex(random_bytes(16)) . time()) . md5('Tortas Josefo' . $correo);
                $url = urlRecuperacion($token);
                $contenido = "Hola, pediste recuperar tu contraseña en Tortas Josefo. Entra a este enlace para elegir una nueva:\n" . $url;
                if ($app->guardarToken($correo, $token)) {
                    $app->enviarCorreo([$correo], 'Recuperación de contraseña', $contenido);
                    $alerta = [
                        'tipo' => 'success',
                        'mensaje' => 'Se envió el correo. Revisa tu bandeja.',
                    ];
                } else {
                    $alerta = ['tipo' => 'danger', 'mensaje' => 'No hay un empleado con ese correo'];
                }
            } else {
                $alerta = ['tipo' => 'danger', 'mensaje' => 'Correo no válido'];
            }
        }
        $formulario = __DIR__ . '/../templates/admin/_form_correo.html';
        break;
    case 'nueva':
        $token = $_GET['token'] ?? '';
        if (strlen($token) == 64 && $app->validarToken($token)) {
            $formulario = __DIR__ . '/../templates/admin/_form_password.html';
            break;
        }
        $alerta = ['tipo' => 'danger', 'mensaje' => 'Token inválido, genera un nuevo token'];
        $formulario = __DIR__ . '/../templates/admin/_form_correo.html';
        break;
    case 'restaurar':
        $token = $_POST['token'] ?? ($_GET['token'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $contrasena2 = $_POST['contrasena2'] ?? '';
        if (isset($_POST['enviar']) && $contrasena !== '' && $contrasena === $contrasena2 && strlen($token) == 64 && $app->validarToken($token) && $app->actualizarContrasena($token, $contrasena)) {
            $alerta = ['tipo' => 'success', 'mensaje' => 'Contraseña actualizada. Ya puedes entrar.'];
            break;
        }
        $alerta = ['tipo' => 'danger', 'mensaje' => 'No se pudo actualizar la contraseña. Deben coincidir y tener al menos 6 caracteres.'];
        $formulario = __DIR__ . '/../templates/admin/_form_password.html';
        break;
    default:
        if (isset($_POST['enviar'])) {
            $empleado = $app->login($_POST['correo'] ?? '', $_POST['password'] ?? '');
            if ($empleado) {
                session_regenerate_id(true);
                $_SESSION['id_empleado'] = $empleado['id'];
                $_SESSION['usuario'] = $empleado['nombre'];
                header('Location: index.php');
                exit;
            }
            $error = 'Correo o contraseña incorrectos';
            $alerta = ['tipo' => 'danger', 'mensaje' => $error];
        }
}
include_once __DIR__ . '/../templates/admin/login.html';
