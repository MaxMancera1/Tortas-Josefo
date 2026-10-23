<?php

require_once __DIR__ . '/../sistema.class.php';

class Promocion extends Sistema {

    function validar($data){
        if (mb_strlen(trim($data['titulo'] ?? '')) < 3) {
            return 'El título debe tener al menos 3 caracteres';
        }
        if (mb_strlen(trim($data['descripcion'] ?? '')) < 10) {
            return 'La descripción debe tener al menos 10 caracteres';
        }
        if (!is_numeric($data['precio'] ?? '') || $data['precio'] <= 0 || $data['precio'] > 9999) {
            return 'El precio debe ser un número entre 1 y 9999';
        }
        $fecha = DateTime::createFromFormat('Y-m-d', $data['vigencia'] ?? '');
        if (!$fecha || $fecha->format('Y-m-d') !== $data['vigencia']) {
            return 'La fecha de vigencia no es válida';
        }
        return null;
    }

    function create($data){
        $error = $this->validar($data);
        if (!$error && $data['vigencia'] < date('Y-m-d')) {
            $error = 'La vigencia no puede ser una fecha pasada';
        }
        if ($error) {
            return $data + ['error' => $error];
        }
        $datos = [];
        $datos['imagen'] = $this->cargarArchivo('imagen', '../../image/uploads/');
        if (is_null($datos['imagen'])) {
            return $data + ['error' => 'Sube una imagen JPG, PNG o WEBP de máximo 2 MB'];
        }
        $sql = "INSERT INTO promociones (titulo, descripcion, precio, vigencia, imagen)
                VALUES (:titulo, :descripcion, :precio, :vigencia, :imagen)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":titulo", $data['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $data['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
        $stmt->bindParam(":vigencia", $data['vigencia'], PDO::PARAM_STR);
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM promociones WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT * FROM promociones ORDER BY vigencia";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $actual = $this->getOne($id);
            $error = $this->validar($data);
            if ($error) {
                return $data + ['id' => $id, 'imagen' => $actual['imagen'] ?? '', 'error' => $error];
            }
            $imagen = $this->cargarArchivo('imagen', '../../image/uploads/');
            if ($this->intentoArchivo('imagen') && is_null($imagen)) {
                return $data + ['id' => $id, 'imagen' => $actual['imagen'] ?? '', 'error' => 'Sube una imagen JPG, PNG o WEBP de máximo 2 MB'];
            }
            if (is_null($imagen)) {
                $imagen = $actual['imagen'] ?? '';
            }
            $sql = "UPDATE promociones SET titulo = :titulo, descripcion = :descripcion, precio = :precio, vigencia = :vigencia, imagen = :imagen WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":titulo", $data['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $data['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
            $stmt->bindParam(":vigencia", $data['vigencia'], PDO::PARAM_STR);
            $stmt->bindParam(":imagen", $imagen, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM promociones WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
