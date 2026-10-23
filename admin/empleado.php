<?php
include_once __DIR__ . '/models/empleado/empleado.class.php';
include_once __DIR__ . '/models/sucursal/sucursal.class.php';
$app = new Empleado();
$app->checarAcceso('Empleados');
$sucursalesApp = new Sucursal();
$sucursales = $sucursalesApp->getAll();
$puestos = $app->puestos;
$roles = $app->getRoles();
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/../templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->create($data);
            if (isset($datos['error'])) {
                include_once __DIR__ . '/../templates/empleado/_form.html';
            } else {
                $empleados = $app->getAll();
                include_once __DIR__ . '/../templates/empleado/index.html';
            }
        } else {
            include_once __DIR__ . '/../templates/empleado/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'] ?? [];
            $datos = $app->update($id, $data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/../templates/empleado/_form.html';
            } else {
                $empleados = $app->getAll();
                include_once __DIR__ . '/../templates/empleado/index.html';
            }
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/../templates/empleado/_form.html';
        }
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $empleados = $app->getAll();
        include_once __DIR__ . '/../templates/empleado/index.html';
        break;
    default:
        $empleados = $app->getAll();
        include_once __DIR__ . '/../templates/empleado/index.html';
}
include_once __DIR__ . '/../templates/admin/footer.html';
