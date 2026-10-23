<?php
include_once __DIR__ . '/models/sucursal/sucursal.class.php';
$app = new Sucursal();
$app->checarAcceso('Sucursales');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->create($data);
            if (isset($datos['error'])) {
                include_once __DIR__ . '/../templates/sucursal/_form.html';
            } else {
                $sucursales = $app->getAll();
                include_once __DIR__ . '/../templates/sucursal/index.html';
            }
        } else {
            include_once __DIR__ . '/../templates/sucursal/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->update($id, $data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/../templates/sucursal/_form.html';
            } else {
                $sucursales = $app->getAll();
                include_once __DIR__ . '/../templates/sucursal/index.html';
            }
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
