<?php

require_once __DIR__ . '/../sistema.class.php';

class Proveedor extends Sistema {

    function validar($data){
        if (mb_strlen(trim($data['razon_social'] ?? '')) < 3) {
            return 'La razón social debe tener al menos 3 caracteres';
        }
        // RFC
        if (!preg_match('/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/u', $data['rfc'] ?? '')) {
            return 'Ingresa un RFC válido';
        }
        if (trim($data['producto'] ?? '') === '') {
            return 'Indica qué surte el proveedor';
        }
        return null;
    }

    function create($data){
        $data['rfc'] = mb_strtoupper(trim($data['rfc'] ?? ''));
        $error = $this->validar($data);
        if ($error) {
            return $data + ['error' => $error];
        }
        $datos = [];
        $datos['logo'] = $this->cargarArchivo('logo', '../../image/uploads/');
        if (is_null($datos['logo'])) {
            return $data + ['error' => 'Sube un logo JPG, PNG o WEBP de máximo 2 MB'];
        }
        $sql = "INSERT INTO proveedores (razon_social, rfc, producto, logo)
                VALUES (:razon_social, :rfc, :producto, :logo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":razon_social", $data['razon_social'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
        $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
        $stmt->bindParam(":logo", $datos['logo'], PDO::PARAM_STR);
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
            $data['rfc'] = mb_strtoupper(trim($data['rfc'] ?? ''));
            $actual = $this->getOne($id);
            $error = $this->validar($data);
            if ($error) {
                return $data + ['id' => $id, 'logo' => $actual['logo'] ?? '', 'error' => $error];
            }
            $logo = $this->cargarArchivo('logo', '../../image/uploads/');
            if ($this->intentoArchivo('logo') && is_null($logo)) {
                return $data + ['id' => $id, 'logo' => $actual['logo'] ?? '', 'error' => 'Sube un logo JPG, PNG o WEBP de máximo 2 MB'];
            }
            if (is_null($logo)) {
                $logo = $actual['logo'] ?? '';
            }
            $sql = "UPDATE proveedores SET razon_social = :razon_social, rfc = :rfc, producto = :producto, logo = :logo WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":razon_social", $data['razon_social'], PDO::PARAM_STR);
            $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
            $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
            $stmt->bindParam(":logo", $logo, PDO::PARAM_STR);
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
