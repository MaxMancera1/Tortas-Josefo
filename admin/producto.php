<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
include_once __DIR__ . '/models/producto/producto.class.php';
$app = new Producto();
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $datos = $app->create($data);
            $productos = $app->getAll();
            include_once __DIR__ . '/../templates/producto/index.html';
        } else {
            include_once __DIR__ . '/../templates/producto/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $datos = $app->update($id, $data);
            $productos = $app->getAll();
            include_once __DIR__ . '/../templates/producto/index.html';
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/../templates/producto/_form.html';
        }
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $productos = $app->getAll();
        include_once __DIR__ . '/../templates/producto/index.html';
        break;
    default:
        $productos = $app->getAll();
        include_once __DIR__ . '/../templates/producto/index.html';
}
include_once __DIR__ . '/../templates/admin/footer.html';
