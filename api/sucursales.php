<?php
require_once __DIR__ . '/_comun.php';
require_once __DIR__ . '/../admin/models/sucursal/sucursal.class.php';
$app = new Sucursal();
$metodo = api_metodo();
$id = api_id();

if ($metodo === 'GET') {
    if ($id === null || $id === '') {
        api_salir($app->getAll());
    }
    $uno = $app->getOne($id);
    $uno ? api_salir($uno) : api_salir(['error' => 'No encontrado'], 404);
}

if ($metodo === 'DELETE') {
    api_salir(['ok' => (bool)$app->db->prepare('DELETE FROM sucursales WHERE id = ?')->execute([$id]), 'id' => $id]);
}

if ($metodo === 'POST' || $metodo === 'PUT') {
    $d = api_entrada();
    if ($id) {
        $act = $app->getOne($id) ?: [];
        $sql = 'UPDATE sucursales SET nombre = ?, direccion = ?, telefono = ? WHERE id = ?';
        $ok = $app->db->prepare($sql)->execute([
            $d['nombre'] ?? $act['nombre'] ?? '',
            $d['direccion'] ?? $act['direccion'] ?? '',
            $d['telefono'] ?? $act['telefono'] ?? '',
            $id,
        ]);
        api_salir(['ok' => (bool)$ok, 'id' => $id] + ($app->getOne($id) ?: []));
    }
    $sql = 'INSERT INTO sucursales (nombre, direccion, telefono) VALUES (?, ?, ?)';
    $app->db->prepare($sql)->execute([
        $d['nombre'] ?? 'Sucursal',
        $d['direccion'] ?? '',
        $d['telefono'] ?? '',
    ]);
    $nuevo = $app->db->lastInsertId();
    api_salir(['ok' => true, 'id' => $nuevo] + ($app->getOne($nuevo) ?: []), 201);
}

api_salir(['error' => 'Método no permitido'], 405);
