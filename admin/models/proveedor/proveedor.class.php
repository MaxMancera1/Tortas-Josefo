<?php

require_once __DIR__ . '/../sistema.class.php';

class Proveedor extends Sistema {

    function create($data){
        $datos = [];
        $sql = "INSERT INTO proveedores (razon_social, rfc, producto)
                VALUES (:razon_social, :rfc, :producto)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":razon_social", $data['razon_social'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
        $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM proveedores WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT * FROM proveedores";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "UPDATE proveedores SET razon_social = :razon_social, rfc = :rfc, producto = :producto WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":razon_social", $data['razon_social'], PDO::PARAM_STR);
            $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
            $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM proveedores WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
