<?php

require_once __DIR__ . '/../sistema.class.php';

class Sucursal extends Sistema {

    function create($data){
        $datos = [];
        $sql = "INSERT INTO sucursales (nombre, direccion, telefono)
                VALUES (:nombre, :direccion, :telefono)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $data['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $data['telefono'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM sucursales WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT * FROM sucursales";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "UPDATE sucursales SET nombre = :nombre, direccion = :direccion, telefono = :telefono WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $data['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $data['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM sucursales WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
