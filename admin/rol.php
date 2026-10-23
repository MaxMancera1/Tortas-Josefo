<?php
include_once __DIR__ . '/models/rol/rol.class.php';
$app = new Rol();
$app->checarAcceso('Roles');
$permisos = $app->getPermisos();
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->create($data);
            if (isset($datos['error'])) {
                include_once __DIR__ . '/../templates/rol/_form.html';
            } else {
                $roles = $app->getAll();
                include_once __DIR__ . '/../templates/rol/index.html';
            }
        } else {
            include_once __DIR__ . '/../templates/rol/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->update($id, $data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/../templates/rol/_form.html';
            } else {
                $roles = $app->getAll();
                include_once __DIR__ . '/../templates/rol/index.html';
            }
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/../templates/rol/_form.html';
        }
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $roles = $app->getAll();
        include_once __DIR__ . '/../templates/rol/index.html';
        break;
    default:
        $roles = $app->getAll();
        include_once __DIR__ . '/../templates/rol/index.html';
}
include_once __DIR__ . '/../templates/admin/footer.html';
