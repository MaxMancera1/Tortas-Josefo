<?php

require_once __DIR__ . '/../sistema.class.php';

class Empleado extends Sistema {

    var $puestos = ['Dueño', 'Encargado', 'Cocinero', 'Cajero', 'Repartidor'];

    // $id = null cuando es create
    function validar($data, $id = null){
        if (mb_strlen(trim($data['nombre'] ?? '')) < 3) {
            return 'El nombre debe tener al menos 3 caracteres';
        }
        if (!in_array($data['puesto'] ?? '', $this->puestos, true)) {
            return 'Elige un puesto de la lista';
        }
        if (!preg_match('/^[0-9]{10}$/', str_replace(' ', '', $data['telefono'] ?? ''))) {
            return 'El teléfono debe tener 10 dígitos';
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sucursales WHERE id = :id");
        $stmt->bindValue(":id", (int) ($data['id_sucursal'] ?? 0), PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            return 'Elige una sucursal';
        }
        // Acceso al panel: solo se valida si escribieron correo
        $correo = trim($data['correo'] ?? '');
        if ($correo === '') {
            return null;
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return 'Ingresa un correo válido';
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM empleados WHERE correo = :correo AND id <> :id");
        $stmt->bindValue(":correo", $correo, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id ?? 0, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return 'Ya existe un empleado con ese correo';
        }
        $password = $data['password'] ?? '';
        $yaTenia = $id !== null && $this->tienePassword($id);
        if ($password === '' && !$yaTenia) {
            return 'Escribe una contraseña para darle acceso';
        }
        if ($password !== '' && mb_strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres';
        }
        if (empty($data['roles'])) {
            return 'Elige al menos un rol para darle acceso';
        }
        return null;
    }

    function create($data){
        $error = $this->validar($data);
        if ($error) {
            unset($data['password']);
            return $data + ['error' => $error];
        }
        $datos = [];
        $datos['foto'] = $this->cargarArchivo('foto', '../../image/uploads/');
        if (is_null($datos['foto'])) {
            unset($data['password']);
            return $data + ['error' => 'Sube una foto JPG, PNG o WEBP de máximo 2 MB'];
        }
        [$correo, $hash] = $this->acceso($data);
        $sql = "INSERT INTO empleados (nombre, puesto, telefono, foto, id_sucursal, correo, password)
                VALUES (:nombre, :puesto, :telefono, :foto, :id_sucursal, :correo, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":puesto", $data['puesto'], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $data['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(":foto", $datos['foto'], PDO::PARAM_STR);
        $stmt->bindParam(":id_sucursal", $data['id_sucursal'], PDO::PARAM_INT);
        $stmt->bindValue(":correo", $correo);
        $stmt->bindValue(":password", $hash);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        $this->syncRoles($datos['id'], $correo ? ($data['roles'] ?? []) : []);
        return $datos;
    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT id, nombre, puesto, telefono, foto, id_sucursal, correo FROM empleados WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($datos) {
                $datos['roles'] = $this->getRolesEmpleado($id);
            }
        }
        return $datos;
    }
    function getAll(){
        $sql = "SELECT empleados.id, empleados.nombre, empleados.puesto, empleados.telefono, empleados.foto,
                       empleados.correo, sucursales.nombre AS sucursal,
                       GROUP_CONCAT(rol.rol ORDER BY rol.rol SEPARATOR ', ') AS roles
                FROM empleados
                LEFT JOIN sucursales ON empleados.id_sucursal = sucursales.id
                LEFT JOIN empleado_rol ON empleado_rol.id_empleado = empleados.id
                LEFT JOIN rol ON rol.id_rol = empleado_rol.id_rol
                GROUP BY empleados.id
                ORDER BY empleados.id";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $actual = $this->getOne($id);
            unset($data['foto']);
            $error = $this->validar($data, $id);
            if ($error) {
                unset($data['password']);
                return $data + ['id' => $id, 'foto' => $actual['foto'] ?? '', 'error' => $error];
            }
            $foto = $this->cargarArchivo('foto', '../../image/uploads/');
            if ($this->intentoArchivo('foto') && is_null($foto)) {
                unset($data['password']);
                return $data + ['id' => $id, 'foto' => $actual['foto'] ?? '', 'error' => 'Sube una foto JPG, PNG o WEBP de máximo 2 MB'];
            }
            if (is_null($foto)) {
                $foto = $actual['foto'] ?? '';
            }
            [$correo, $hash] = $this->acceso($data);
            // contraseña vacía = se conserva la actual; sin correo = se quita el acceso
            $sql = "UPDATE empleados SET nombre = :nombre, puesto = :puesto, telefono = :telefono, foto = :foto,
                           id_sucursal = :id_sucursal, correo = :correo"
                 . ($hash !== null || $correo === null ? ", password = :password" : "")
                 . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(":puesto", $data['puesto'], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $data['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(":foto", $foto, PDO::PARAM_STR);
            $stmt->bindParam(":id_sucursal", $data['id_sucursal'], PDO::PARAM_INT);
            $stmt->bindValue(":correo", $correo);
            if ($hash !== null || $correo === null) {
                $stmt->bindValue(":password", $hash);
            }
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
            $this->syncRoles($id, $correo ? ($data['roles'] ?? []) : []);
        }
        return $afectados;
    }
    function delete($id){
        $afectados = 0;
        // nadie se puede borrar a sí mismo (se quedaría sin poder entrar)
        if (is_numeric($id) && (int)$id !== (int)($_SESSION['id_empleado'] ?? 0)){
            $stmt = $this->db->prepare("DELETE FROM empleado_rol WHERE id_empleado = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = "DELETE FROM empleados WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }

    function login($correo, $password){
        $sql = "SELECT * FROM empleados WHERE correo = :correo AND password IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($empleado && password_verify($password, $empleado['password'])) {
            return $empleado;
        }
        return false;
    }

    // [correo o null, hash o null] según lo que venga en el form
    private function acceso($data){
        $correo = trim($data['correo'] ?? '');
        if ($correo === '') {
            return [null, null];
        }
        $password = $data['password'] ?? '';
        return [$correo, $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null];
    }
    private function tienePassword($id){
        $stmt = $this->db->prepare("SELECT password IS NOT NULL FROM empleados WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    function getRoles(){
        return $this->db->query("SELECT id_rol, rol FROM rol ORDER BY rol")->fetchAll(PDO::FETCH_ASSOC);
    }
    function getRolesEmpleado($id){
        $stmt = $this->db->prepare("SELECT id_rol FROM empleado_rol WHERE id_empleado = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
    // borra los roles del empleado y vuelve a guardar los que vienen marcados
    function syncRoles($idEmpleado, $ids){
        $stmt = $this->db->prepare("DELETE FROM empleado_rol WHERE id_empleado = :id");
        $stmt->bindParam(":id", $idEmpleado, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT IGNORE INTO empleado_rol (id_empleado, id_rol)
                                    SELECT :id_empleado, id_rol FROM rol WHERE id_rol = :id_rol");
        foreach ((array)$ids as $idRol) {
            if (is_numeric($idRol)) {
                $stmt->bindValue(":id_empleado", $idEmpleado, PDO::PARAM_INT);
                $stmt->bindValue(":id_rol", $idRol, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
}

?>
