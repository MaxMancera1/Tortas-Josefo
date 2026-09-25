<?php

    require_once __DIR__ . '/config.php';
    class Sistema {
    var $db;
    function __construct() {
        //Constructor de la clase
        //DSN de la base de datos
        $this -> db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
        $this -> db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    }


?>
