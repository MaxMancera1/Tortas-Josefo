<?php
require_once __DIR__ . '/_comun.php';
require_once __DIR__ . '/../admin/models/promocion/promocion.class.php';
$app = new Promocion();
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
    api_salir(['ok' => (bool)$app->db->prepare('DELETE FROM promociones WHERE id = ?')->execute([$id]), 'id' => $id]);
}

if ($metodo === 'POST' || $metodo === 'PUT') {
    $d = api_entrada();
    if ($id) {
        $act = $app->getOne($id) ?: [];
        $sql = 'UPDATE promociones SET titulo = ?, descripcion = ?, precio = ?, vigencia = ?, imagen = ? WHERE id = ?';
        $ok = $app->db->prepare($sql)->execute([
            $d['titulo'] ?? $act['titulo'] ?? '',
            $d['descripcion'] ?? $act['descripcion'] ?? '',
            $d['precio'] ?? $act['precio'] ?? 0,
            $d['vigencia'] ?? $act['vigencia'] ?? date('Y-m-d'),
            $d['imagen'] ?? $act['imagen'] ?? 'promo-combo.png',
            $id,
        ]);
        api_salir(['ok' => (bool)$ok, 'id' => $id] + ($app->getOne($id) ?: []));
    }
    $sql = 'INSERT INTO promociones (titulo, descripcion, precio, vigencia, imagen) VALUES (?, ?, ?, ?, ?)';
    $app->db->prepare($sql)->execute([
        $d['titulo'] ?? 'Promoción',
        $d['descripcion'] ?? '',
        $d['precio'] ?? 0,
        $d['vigencia'] ?? date('Y-m-d'),
        $d['imagen'] ?? 'promo-combo.png',
    ]);
    $nuevo = $app->db->lastInsertId();
    api_salir(['ok' => true, 'id' => $nuevo] + ($app->getOne($nuevo) ?: []), 201);
}

api_salir(['error' => 'Método no permitido'], 405);
