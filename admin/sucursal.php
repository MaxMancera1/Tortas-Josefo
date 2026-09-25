<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
include_once __DIR__ . '/models/sucursal/sucursal.class.php';
$app = new Sucursal();
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $datos = $app->create($data);
            $sucursales = $app->getAll();
            include_once __DIR__ . '/../templates/sucursal/index.html';
        } else {
            include_once __DIR__ . '/../templates/sucursal/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $datos = $app->update($id, $data);
            $sucursales = $app->getAll();
            include_once __DIR__ . '/../templates/sucursal/index.html';
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/../templates/sucursal/_form.html';
        }
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $sucursales = $app->getAll();
        include_once __DIR__ . '/../templates/sucursal/index.html';
        break;
    default:
        $sucursales = $app->getAll();
        include_once __DIR__ . '/../templates/sucursal/index.html';
}
include_once __DIR__ . '/../templates/admin/footer.html';
