<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-HTTP-Method-Override');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function api_metodo(){
    $m = strtoupper($_SERVER['REQUEST_METHOD']);
    $override = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '');
    if ($override) {
        return $override;
    }
    return $m;
}

function api_id(){
    return $_GET['id'] ?? null;
}

function api_entrada(){
    $crudo = file_get_contents('php://input');
    $json = json_decode($crudo, true);
    if (is_array($json)) {
        return $json;
    }
    return $_POST;
}

function api_salir($datos, $codigo = 200){
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
