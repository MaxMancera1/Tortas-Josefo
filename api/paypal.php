<?php
header('Content-Type: application/json; charset=utf-8');
$cfg = __DIR__ . '/../admin/models/config.php';
require_once is_file($cfg) ? $cfg : __DIR__ . '/../admin/models/config.example.php';
$id = defined('PAYPAL_CLIENT_ID') ? PAYPAL_CLIENT_ID : '';
if ($id === '' || strncmp($id, 'PON_AQUI', 8) === 0) {
    $id = '';
}
echo json_encode(['client_id' => $id]);
