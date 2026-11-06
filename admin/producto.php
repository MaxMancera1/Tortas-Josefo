<?php
include_once __DIR__ . '/models/producto/producto.class.php';
$app = new Producto();
$app->checarAcceso('Productos');
$categorias = $app->categorias;
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
if ($accion === 'pdf') {
    $app->reportePdf();
}
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->create($data);
            if (isset($datos['error'])) {
                include_once __DIR__ . '/../templates/producto/_form.html';
            } else {
                $productos = $app->getAll();
                include_once __DIR__ . '/../templates/producto/index.html';
            }
        } else {
            include_once __DIR__ . '/../templates/producto/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->update($id, $data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/../templates/producto/_form.html';
            } else {
                $productos = $app->getAll();
                include_once __DIR__ . '/../templates/producto/index.html';
            }
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
