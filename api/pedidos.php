<?php
require_once __DIR__ . '/_comun.php';
require_once __DIR__ . '/../admin/models/sistema.class.php';

$app = new Sistema();
$metodo = api_metodo();

if ($metodo === 'GET') {
    $id = api_id();
    if (!ctype_digit((string) $id)) {
        api_salir(['error' => 'No encontrado'], 404);
    }
    $stmt = $app->db->prepare('SELECT id, nombre, pedido, total, entrega, direccion, metodo_pago, estatus, creado FROM pedidos WHERE id = ?');
    $stmt->execute([$id]);
    $uno = $stmt->fetch(PDO::FETCH_ASSOC);
    $uno ? api_salir($uno) : api_salir(['error' => 'No encontrado'], 404);
}

if ($metodo === 'POST') {
    $d = api_entrada();
    $nombre = trim($d['nombre'] ?? '');
    $idProducto = $d['producto_id'] ?? '';
    $entrega = $d['entrega'] ?? '';
    $direccion = trim($d['direccion'] ?? '');
    $metodoPago = $d['metodo_pago'] ?? '';
    $paypal = trim($d['paypal_orden'] ?? '');
    if (mb_strlen($nombre) < 2 || !ctype_digit((string) $idProducto)) {
        api_salir(['error' => 'Revisa el nombre y lo que pediste'], 400);
    }
    $prod = $app->db->prepare('SELECT nombre, precio FROM productos WHERE id = ?');
    $prod->execute([$idProducto]);
    $producto = $prod->fetch(PDO::FETCH_ASSOC);
    if (!$producto) {
        api_salir(['error' => 'Ese producto ya no está en el menú'], 400);
    }
    if (!in_array($entrega, ['recoger', 'domicilio'], true) || !in_array($metodoPago, ['efectivo', 'transferencia', 'paypal'], true)) {
        api_salir(['error' => 'Elige entrega y forma de pago'], 400);
    }
    if ($entrega === 'domicilio' && mb_strlen($direccion) < 5) {
        api_salir(['error' => 'Escribe la dirección de entrega'], 400);
    }
    if ($entrega === 'recoger') {
        $direccion = '';
    }
    if ($metodoPago === 'paypal' && !preg_match('/^[A-Za-z0-9]{8,40}$/', $paypal)) {
        api_salir(['error' => 'El pago de PayPal no se confirmó'], 400);
    }
    $estatus = $metodoPago === 'paypal' ? 'pagado' : 'pendiente';
    $sql = 'INSERT INTO pedidos (nombre, pedido, total, entrega, direccion, metodo_pago, estatus, paypal_orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $app->db->prepare($sql)->execute([
        $nombre,
        $producto['nombre'],
        $producto['precio'],
        $entrega,
        $direccion,
        $metodoPago,
        $estatus,
        $metodoPago === 'paypal' ? $paypal : null,
    ]);
    api_salir(['ok' => true, 'id' => $app->db->lastInsertId(), 'estatus' => $estatus], 201);
}

api_salir(['error' => 'Método no permitido'], 405);
