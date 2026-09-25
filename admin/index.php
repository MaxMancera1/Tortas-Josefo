<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
include_once __DIR__ . '/../templates/admin/header.html';
include_once __DIR__ . '/../templates/admin/index.html';
include_once __DIR__ . '/../templates/admin/footer.html';
