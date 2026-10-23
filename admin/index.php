<?php
include_once __DIR__ . '/models/sistema.class.php';
include_once __DIR__ . '/models/producto/producto.class.php';
$app = new Sistema();
$app->checarAcceso();
$dataCategorias = (new Producto())->getProductosCategoria();
$dataPrecios = (new Producto())->getProductosPrecios();

$categorias = array_map(fn($f) => [$f['categoria'], (int)$f['cantidad']], $dataCategorias);
$precios = array_map(fn($p) => [$p['nombre'], (float)$p['precio']], $dataPrecios);
$total = array_sum(array_column($categorias, 1));
$nCat = count($categorias);
$liderCat = $categorias[0] ?? ['—', 0];
$pctLider = $total ? round($liderCat[1] * 100 / $total) : 0;
$caro = $precios[0] ?? ['—', 0];
$promedio = $precios ? round(array_sum(array_column($precios, 1)) / count($precios)) : 0;
$coloresDona = ['#EA580C', '#F97316', '#FB923C', '#FDBA74', '#C2410C'];

include_once __DIR__ . '/../templates/admin/header.html';
include_once __DIR__ . '/../templates/admin/index.html';
include_once __DIR__ . '/../templates/admin/footer.html';
