<?php
require_once __DIR__ . '/_comun.php';
require_once __DIR__ . '/../admin/models/producto/producto.class.php';
$app = new Producto();
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
    api_salir(['ok' => (bool)$app->db->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]), 'id' => $id]);
}

if ($metodo === 'POST' || $metodo === 'PUT') {
    $d = api_entrada();
    // Si viene un archivo, se guarda en image/uploads y se usa ese nombre.
    // Si no viene archivo, sigue valiendo el texto "foto" (el cliente de Python).
    if ($app->intentoArchivo('foto')) {
        $subida = $app->cargarArchivo('foto', '../../image/uploads/');
        if (!$subida) {
            api_salir(['error' => 'La foto debe ser JPG, PNG o WEBP'], 400);
        }
        $d['foto'] = $subida;
    }
    if ($id) {
        $act = $app->getOne($id) ?: [];
        $sql = 'UPDATE productos SET nombre = ?, categoria = ?, precio = ?, foto = ? WHERE id = ?';
        $ok = $app->db->prepare($sql)->execute([
            $d['nombre'] ?? $act['nombre'] ?? '',
            $d['categoria'] ?? $act['categoria'] ?? 'Tortas',
            $d['precio'] ?? $act['precio'] ?? 0,
            $d['foto'] ?? $act['foto'] ?? 'torta-cubana.png',
            $id,
        ]);
        api_salir(['ok' => (bool)$ok, 'id' => $id] + ($app->getOne($id) ?: []));
    }
    $sql = 'INSERT INTO productos (nombre, categoria, precio, foto) VALUES (?, ?, ?, ?)';
    $app->db->prepare($sql)->execute([
        $d['nombre'] ?? 'Producto',
        $d['categoria'] ?? 'Tortas',
        $d['precio'] ?? 0,
        $d['foto'] ?? 'torta-cubana.png',
    ]);
    $nuevo = $app->db->lastInsertId();
    api_salir(['ok' => true, 'id' => $nuevo] + ($app->getOne($nuevo) ?: []), 201);
}

api_salir(['error' => 'Método no permitido'], 405);
