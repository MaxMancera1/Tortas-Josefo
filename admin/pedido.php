<?php
include_once __DIR__ . '/models/sistema.class.php';
$app = new Sistema();
$app->checarAcceso('Pedidos');

if (isset($_POST['pagar']) && ctype_digit((string) ($_POST['id'] ?? ''))) {
    $app->db->prepare("UPDATE pedidos SET estatus = 'pagado' WHERE id = ? AND estatus = 'pendiente'")->execute([$_POST['id']]);
    header('Location: pedido.php');
    exit;
}

$pedidos = $app->db->query('SELECT * FROM pedidos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
include_once __DIR__ . '/../templates/admin/header.html';
include_once __DIR__ . '/../templates/pedido/index.html';
include_once __DIR__ . '/../templates/admin/footer.html';
