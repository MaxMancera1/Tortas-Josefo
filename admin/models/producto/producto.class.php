<?php

require_once __DIR__ . '/../sistema.class.php';

class Producto extends Sistema {

    var $categorias = ['Tortas', 'Hamburguesas', 'Quesadillas', 'Extras', 'Bebidas'];

    function validar($data){
        if (mb_strlen(trim($data['nombre'] ?? '')) < 3) {
            return 'El nombre debe tener al menos 3 caracteres';
        }
        if (!in_array($data['categoria'] ?? '', $this->categorias, true)) {
            return 'Elige una categoría de la lista';
        }
        if (!is_numeric($data['precio'] ?? '') || $data['precio'] <= 0 || $data['precio'] > 9999) {
            return 'El precio debe ser un número entre 1 y 9999';
        }
        return null;
    }

    function create($data){
        $error = $this->validar($data);
        if ($error) {
            return $data + ['error' => $error];
        }
        $datos = [];
        $datos['foto'] = $this->cargarArchivo('foto', '../../image/uploads/');
        if (is_null($datos['foto'])) {
            return $data + ['error' => 'Sube una foto JPG, PNG o WEBP de máximo 2 MB'];
        }
        $sql = "INSERT INTO productos (nombre, categoria, precio, foto)
                VALUES (:nombre, :categoria, :precio, :foto)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":categoria", $data['categoria'], PDO::PARAM_STR);
        $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
        $stmt->bindParam(":foto", $datos['foto'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT * FROM productos";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function getProductosCategoria(){
        $sql = "SELECT categoria, COUNT(*) AS cantidad
                FROM productos
                GROUP BY categoria
                ORDER BY cantidad DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function getProductosPrecios(){
        $sql = "SELECT nombre, precio FROM productos ORDER BY precio DESC, nombre";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $actual = $this->getOne($id);
            $error = $this->validar($data);
            if ($error) {
                return $data + ['id' => $id, 'foto' => $actual['foto'] ?? '', 'error' => $error];
            }
            $foto = $this->cargarArchivo('foto', '../../image/uploads/');
            if ($this->intentoArchivo('foto') && is_null($foto)) {
                return $data + ['id' => $id, 'foto' => $actual['foto'] ?? '', 'error' => 'Sube una foto JPG, PNG o WEBP de máximo 2 MB'];
            }
            if (is_null($foto)) {
                $foto = $actual['foto'] ?? '';
            }
            $sql = "UPDATE productos SET nombre = :nombre, categoria = :categoria, precio = :precio, foto = :foto WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(":categoria", $data['categoria'], PDO::PARAM_STR);
            $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
            $stmt->bindParam(":foto", $foto, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
