<?php

require_once __DIR__ . '/../sistema.class.php';

class Rol extends Sistema {

    function validar($data, $id = null){
        if (mb_strlen(trim($data['rol'] ?? '')) < 3) {
            return 'El nombre del rol debe tener al menos 3 caracteres';
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM rol WHERE rol = :rol AND id_rol <> :id");
        $stmt->bindValue(":rol", trim($data['rol']), PDO::PARAM_STR);
        $stmt->bindValue(":id", $id ?? 0, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return 'Ya existe un rol con ese nombre';
        }
        if (empty($data['permisos'])) {
            return 'Marca al menos un permiso';
        }
        return null;
    }

    function create($data){
        $error = $this->validar($data);
        if ($error) {
            return $data + ['error' => $error];
        }
        $datos = [];
        $rol = trim($data['rol']);
        $stmt = $this->db->prepare("INSERT INTO rol (rol) VALUES (:rol)");
        $stmt->bindParam(":rol", $rol, PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        $this->syncPermisos($datos['id'], $data['permisos']);
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $stmt = $this->db->prepare("SELECT id_rol AS id, rol FROM rol WHERE id_rol = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($datos) {
                $datos['permisos'] = $this->getPermisosRol($id);
            }
        }
        return $datos;
    }
    function getAll(){
        $sql = "SELECT rol.id_rol, rol.rol,
                       GROUP_CONCAT(permiso.permiso ORDER BY permiso.id_permiso SEPARATOR ', ') AS permisos,
                       (SELECT COUNT(*) FROM empleado_rol WHERE empleado_rol.id_rol = rol.id_rol) AS empleados
                FROM rol
                LEFT JOIN rol_permiso ON rol_permiso.id_rol = rol.id_rol
                LEFT JOIN permiso ON permiso.id_permiso = rol_permiso.id_permiso
                GROUP BY rol.id_rol
                ORDER BY rol.id_rol";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $error = $this->validar($data, $id);
            if ($error) {
                return $data + ['id' => $id, 'error' => $error];
            }
            $rol = trim($data['rol']);
            $stmt = $this->db->prepare("UPDATE rol SET rol = :rol WHERE id_rol = :id");
            $stmt->bindParam(":rol", $rol, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
            $this->syncPermisos($id, $data['permisos']);
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            foreach (['rol_permiso', 'empleado_rol', 'rol'] as $tabla) {
                $stmt = $this->db->prepare("DELETE FROM $tabla WHERE id_rol = :id");
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $afectados = $stmt->execute();
            }
        }
        return $afectados;
    }

    function getPermisos(){
        return $this->db->query("SELECT id_permiso, permiso FROM permiso ORDER BY id_permiso")->fetchAll(PDO::FETCH_ASSOC);
    }
    function getPermisosRol($id){
        $stmt = $this->db->prepare("SELECT id_permiso FROM rol_permiso WHERE id_rol = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
    function syncPermisos($idRol, $ids){
        $stmt = $this->db->prepare("DELETE FROM rol_permiso WHERE id_rol = :id");
        $stmt->bindParam(":id", $idRol, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT IGNORE INTO rol_permiso (id_rol, id_permiso)
                                    SELECT :id_rol, id_permiso FROM permiso WHERE id_permiso = :id_permiso");
        foreach ((array)$ids as $idPermiso) {
            if (is_numeric($idPermiso)) {
                $stmt->bindValue(":id_rol", $idRol, PDO::PARAM_INT);
                $stmt->bindValue(":id_permiso", $idPermiso, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
}

?>
