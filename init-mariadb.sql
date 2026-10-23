-- RFC ficticios, solo de práctica
CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(255) NOT NULL,
  rfc VARCHAR(13) NOT NULL,
  producto VARCHAR(255) NOT NULL,
  logo VARCHAR(255) NOT NULL DEFAULT ''
);

INSERT INTO proveedores (razon_social, rfc, producto) VALUES
  ('Panadería La Espiga de Cortazar', 'PEC900101AB1', 'Pan bolillo y telera'),
  ('Embutidos del Bajío SA de CV', 'EBA850315XY2', 'Jamón, pierna y milanesa'),
  ('Embotelladora Coca-Cola Celaya', 'ECC920720QW3', 'Coca-Cola y refrescos'),
  ('Distribuidora Pepsi Guanajuato', 'DPG010203ER4', 'Pepsi y refrescos'),
  ('Lácteos San Juan', 'LSJ950505TY5', 'Queso asadero'),
  ('Verduras Doña Chela', 'VDC880808UI6', 'Aguacate, jitomate y cebolla');

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  categoria VARCHAR(100) NOT NULL,
  precio DECIMAL(8,2) NOT NULL,
  foto VARCHAR(255) NOT NULL DEFAULT ''
);

-- las fotos viven en image/uploads/
INSERT INTO productos (nombre, categoria, precio, foto) VALUES
  ('Torta de milanesa', 'Tortas', 50, 'torta-milanesa.png'),
  ('Torta de pierna', 'Tortas', 50, 'torta-pierna.png'),
  ('Torta cubana', 'Tortas', 50, 'torta-cubana.png'),
  ('Torta de jamón', 'Tortas', 50, 'torta-jamon.png'),
  ('Hamburguesa', 'Hamburguesas', 55, 'hamburguesa.png'),
  ('Quesadilla', 'Quesadillas', 35, 'quesadilla.png'),
  ('Papas', 'Extras', 25, 'papas.png'),
  ('Agua de jamaica', 'Bebidas', 15, 'jamaica.png'),
  ('Agua de horchata', 'Bebidas', 15, 'horchata.png'),
  ('Hamburguesa con queso', 'Hamburguesas', 65, 'hamburguesa-queso.png'),
  ('Quesadilla de jamón', 'Quesadillas', 40, 'quesadilla.png');

CREATE TABLE IF NOT EXISTS sucursales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  telefono VARCHAR(20) NOT NULL
);

INSERT INTO sucursales (nombre, direccion, telefono) VALUES
  ('Centro', 'Calle Mariano Matamoros 100, Centro, Cortazar, Gto.', '411 103 8297'),
  ('Paseo de la Juventud', 'Antonio Madrazo y Colegio Militar, frente al Centro Cultural, Cortazar, Gto.', '411 103 8297');

CREATE TABLE IF NOT EXISTS empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  puesto VARCHAR(50) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  foto VARCHAR(255) NOT NULL,
  id_sucursal INT NOT NULL,
  -- acceso al panel (opcional): sin correo el empleado no puede entrar
  correo VARCHAR(150) NULL UNIQUE,
  password VARCHAR(255) NULL
);

-- passwords guardados con password_hash(): josefo123, gisela123, cajero123
INSERT INTO empleados (nombre, puesto, telefono, foto, id_sucursal, correo, password) VALUES
  ('Josefo', 'Dueño', '411 103 8297', 'usuario-josefo.jpg', 2, 'josefo@tortasjosefo.com', '$2y$10$hRGcZZrQm0b9ZZOQXfWiTewb4.kPNiQmo5w6J0KMWeyS1SeQyzGCC'),
  ('Gisela', 'Encargado', '411 103 8297', 'usuario-gisela.jpg', 1, 'gisela@tortasjosefo.com', '$2y$10$JA4Xyg6TkEHZSA0WvNfnXeqSIG7cHBu9WrKYjEDsrRhsRIH0mZfYu'),
  ('Carlos', 'Cajero', '411 103 8297', 'usuario-cajero.jpg', 1, 'cajero@tortasjosefo.com', '$2y$10$2ZCIvUseQfsbgvs1mS0RQuFJarPU/mZbNkkVZvt9YS9dytIkFpB5G');

-- Empleados -> roles -> permisos
-- los permisos son fijos (uno por sección del panel): el nombre es el que revisa
-- cada controlador con checarAcceso(); los roles se arman en el CRUD de Roles
CREATE TABLE IF NOT EXISTS permiso (
  id_permiso INT AUTO_INCREMENT PRIMARY KEY,
  permiso VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS rol (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  rol VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS rol_permiso (
  id_rol INT NOT NULL,
  id_permiso INT NOT NULL,
  PRIMARY KEY (id_rol, id_permiso)
);

CREATE TABLE IF NOT EXISTS empleado_rol (
  id_empleado INT NOT NULL,
  id_rol INT NOT NULL,
  PRIMARY KEY (id_empleado, id_rol)
);

INSERT IGNORE INTO permiso (permiso) VALUES
  ('Proveedores'), ('Productos'), ('Sucursales'), ('Empleados'), ('Promociones'),
  ('Roles');

INSERT IGNORE INTO rol (rol) VALUES ('Dueño'), ('Encargado'), ('Cajero');

-- Dueño: todo
INSERT IGNORE INTO rol_permiso (id_rol, id_permiso)
  SELECT rol.id_rol, permiso.id_permiso FROM rol, permiso WHERE rol.rol = 'Dueño';
-- Encargado: los catálogos del puesto
INSERT IGNORE INTO rol_permiso (id_rol, id_permiso)
  SELECT rol.id_rol, permiso.id_permiso FROM rol, permiso
  WHERE rol.rol = 'Encargado' AND permiso.permiso IN ('Proveedores', 'Productos', 'Sucursales', 'Empleados', 'Promociones');
-- Cajero: lo que vende
INSERT IGNORE INTO rol_permiso (id_rol, id_permiso)
  SELECT rol.id_rol, permiso.id_permiso FROM rol, permiso
  WHERE rol.rol = 'Cajero' AND permiso.permiso IN ('Productos', 'Promociones');

INSERT IGNORE INTO empleado_rol (id_empleado, id_rol)
  SELECT empleados.id, rol.id_rol FROM empleados, rol
  WHERE (empleados.correo = 'josefo@tortasjosefo.com' AND rol.rol = 'Dueño')
     OR (empleados.correo = 'gisela@tortasjosefo.com' AND rol.rol = 'Encargado')
     OR (empleados.correo = 'cajero@tortasjosefo.com' AND rol.rol = 'Cajero');

CREATE TABLE IF NOT EXISTS promociones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  precio DECIMAL(8,2) NOT NULL,
  vigencia DATE NOT NULL,
  imagen VARCHAR(255) NOT NULL
);

INSERT INTO promociones (titulo, descripcion, precio, vigencia, imagen) VALUES
  ('3 tortas = refresco', 'En la compra de 3 tortas, el refresco va gratis.', 150, '2026-12-31', 'promo-refresco.png'),
  ('2 hamburguesas $100', 'Dos sencillas por $100 (ahorras $10). Cualquier día.', 100, '2026-12-31', 'hamburguesa.png'),
  ('Quesadilla + jamaica', 'Una quesadilla de queso y un agua de jamaica.', 50, '2026-11-30', 'quesadilla.png'),
  ('Combo torta + jamaica', 'Cualquier torta sencilla con agua de jamaica.', 65, '2026-12-31', 'promo-combo.png');
