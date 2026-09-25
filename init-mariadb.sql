-- RFC ficticios, solo de práctica
CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(255) NOT NULL,
  rfc VARCHAR(13) NOT NULL,
  producto VARCHAR(255) NOT NULL
);

INSERT INTO proveedores (razon_social, rfc, producto) VALUES
  ('Panadería La Espiga de Cortazar', 'PEC900101AB1', 'Pan bolillo y telera'),
  ('Embutidos del Bajío SA de CV', 'EBA850315XY2', 'Jamón, pierna y milanesa'),
  ('Embotelladora Coca-Cola Celaya', 'ECC920720QW3', 'Coca-Cola y refrescos'),
  ('Distribuidora Pepsi Guanajuato', 'DPG010203ER4', 'Pepsi y refrescos'),
  ('Lácteos San Juan', 'LSJ950505TY5', 'Queso asadero'),
  ('Verduras Doña Chela', 'VDC880808UI6', 'Aguacate, jitomate y cebolla');

-- password guardado con password_hash(); la contraseña es: josefo123
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO usuarios (nombre, correo, password) VALUES
  ('Josefo', 'josefo@tortasjosefo.com', '$2y$10$hRGcZZrQm0b9ZZOQXfWiTewb4.kPNiQmo5w6J0KMWeyS1SeQyzGCC');

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  categoria VARCHAR(100) NOT NULL,
  precio DECIMAL(8,2) NOT NULL
);

INSERT INTO productos (nombre, categoria, precio) VALUES
  ('Torta de milanesa', 'Tortas', 50),
  ('Torta de pierna', 'Tortas', 50),
  ('Torta cubana', 'Tortas', 50),
  ('Torta de jamón', 'Tortas', 50),
  ('Hamburguesa', 'Hamburguesas', 55),
  ('Quesadilla', 'Quesadillas', 35),
  ('Papas', 'Extras', 25),
  ('Agua de jamaica', 'Bebidas', 15),
  ('Agua de horchata', 'Bebidas', 15);

CREATE TABLE IF NOT EXISTS sucursales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  telefono VARCHAR(20) NOT NULL
);

INSERT INTO sucursales (nombre, direccion, telefono) VALUES
  ('Centro', 'Calle Mariano Matamoros 100, Centro, Cortazar, Gto.', '411 103 8297'),
  ('Paseo de la Juventud', 'Antonio Madrazo y Colegio Militar, frente al Centro Cultural, Cortazar, Gto.', '411 103 8297');
