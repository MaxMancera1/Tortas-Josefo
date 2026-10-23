<?php
include_once __DIR__ . '/models/sistema.class.php';
$app = new Sistema();
$app->checarAcceso();
include_once __DIR__ . '/../templates/admin/header.html';
include_once __DIR__ . '/../templates/admin/index.html';
include_once __DIR__ . '/../templates/admin/footer.html';
