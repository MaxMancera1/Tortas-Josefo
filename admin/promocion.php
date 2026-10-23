<?php
include_once __DIR__ . '/models/promocion/promocion.class.php';
$app = new Promocion();
$app->checarAcceso('Promociones');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->create($data);
            if (isset($datos['error'])) {
                include_once __DIR__ . '/../templates/promocion/_form.html';
            } else {
                $promociones = $app->getAll();
                include_once __DIR__ . '/../templates/promocion/index.html';
            }
        } else {
            include_once __DIR__ . '/../templates/promocion/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->update($id, $data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/../templates/promocion/_form.html';
            } else {
                $promociones = $app->getAll();
                include_once __DIR__ . '/../templates/promocion/index.html';
            }
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/../templates/promocion/_form.html';
        }
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $promociones = $app->getAll();
        include_once __DIR__ . '/../templates/promocion/index.html';
        break;
    default:
        $promociones = $app->getAll();
        include_once __DIR__ . '/../templates/promocion/index.html';
}
include_once __DIR__ . '/../templates/admin/footer.html';
