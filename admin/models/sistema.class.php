<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    class Sistema {
    var $db;
    function __construct() {
        //Constructor de la clase
        //DSN de la base de datos
        $this -> db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
        $this -> db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function cargarArchivo($nombre, $ruta){
        if (!isset($_FILES[$nombre]) || $_FILES[$nombre]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $archivo = $_FILES[$nombre];
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        // getimagesize revisa el contenido real; el 'type' de $_FILES lo manda el navegador y se puede falsificar
        if (!in_array($ext, $extensions, true) || getimagesize($archivo['tmp_name']) === false) {
            return null;
        }
        $destinoDir = __DIR__ . '/' . $ruta;
        if (!is_dir($destinoDir)) {
            mkdir($destinoDir, 0777, true);
        }
        // nombre único para que una foto nueva no pise a otra con el mismo nombre
        $nombreArchivo = uniqid() . '.' . $ext;
        if (!move_uploaded_file($archivo['tmp_name'], $destinoDir . $nombreArchivo)) {
            return null;
        }
        return $nombreArchivo;
    }

    function intentoArchivo($nombre){
        return isset($_FILES[$nombre]) && $_FILES[$nombre]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    // Permisos que le dan sus roles al empleado: empleado -> empleado_rol -> rol_permiso -> permiso
    function permisosDe($idEmpleado){
        $sql = "SELECT DISTINCT permiso.permiso
                FROM empleado_rol
                JOIN rol_permiso ON rol_permiso.id_rol = empleado_rol.id_rol
                JOIN permiso ON permiso.id_permiso = rol_permiso.id_permiso
                WHERE empleado_rol.id_empleado = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $idEmpleado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Se llama al inicio de cada controlador. Sin sesión manda al login;
    // sin el permiso pedido muestra "No tienes permiso" y corta.
    function checarAcceso($permiso = null){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_empleado'])) {
            header('Location: login.php');
            exit;
        }
        // se recalculan en cada página para que un cambio de rol aplique sin volver a entrar
        $_SESSION['permisos'] = $this->permisosDe($_SESSION['id_empleado']);
        if ($permiso && !in_array($permiso, $_SESSION['permisos'], true)) {
            http_response_code(403);
            include __DIR__ . '/../../templates/admin/header.html';
            include __DIR__ . '/../../templates/admin/sin_permiso.html';
            include __DIR__ . '/../../templates/admin/footer.html';
            exit;
        }
    }

    function enviarCorreo($destinatarios, $asunto, $contenido, $adjunto = null){
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Timeout = 8;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            foreach ((array)$destinatarios as $destinatario) {
                $mail->addAddress($destinatario);
            }
            $mail->Subject = $asunto;
            $mail->Body = $contenido;
            if ($adjunto) {
                $mail->addAttachment($adjunto);
            }
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
    }


?>
