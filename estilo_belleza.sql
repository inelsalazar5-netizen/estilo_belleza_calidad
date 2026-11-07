
CREATE DATABASE IF NOT EXISTS estilo_belleza
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE estilo_belleza;

-- TABLA DE USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','user') DEFAULT 'user'
);

-- TABLA DE SERVICIOS
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    duracion INT NOT NULL,
    imagen VARCHAR(255)
);

-- TABLA DE EMPLEADOS
CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    estado ENUM('activo','inactivo') DEFAULT 'activo'
);

-- TABLA DE CITAS
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    id_servicio INT NOT NULL,
    id_empleado INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada','completada')
        DEFAULT 'pendiente',
    FOREIGN KEY (id_servicio) REFERENCES servicios(id),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id)
);

-- SERVICIOS DE EJEMPLO
INSERT INTO servicios (nombre, descripcion, precio, duracion, imagen)
SELECT 'Cortes', 'Corte y estilizado de cabello.', 250.00, 45, 'cortes.jpg'
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Cortes');

INSERT INTO servicios (nombre, descripcion, precio, duracion, imagen)
SELECT 'Manicure', 'Cuidado y decoración de uñas.', 200.00, 60, 'manicure.jpg'
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Manicure');

INSERT INTO servicios (nombre, descripcion, precio, duracion, imagen)
SELECT 'Maquillaje', 'Maquillaje para ocasiones especiales.', 500.00, 90, 'maquillaje.jpg'
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Maquillaje');

INSERT INTO servicios (nombre, descripcion, precio, duracion, imagen)
SELECT 'Peinados', 'Peinados para eventos y ocasiones especiales.', 350.00, 60, 'peinados.jpg'
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Peinados');

-- EMPLEADOS DE EJEMPLO
INSERT INTO empleados (nombre, telefono, especialidad)
SELECT 'María López', '88880001', 'Cortes'
WHERE NOT EXISTS (SELECT 1 FROM empleados WHERE nombre = 'María López');

INSERT INTO empleados (nombre, telefono, especialidad)
SELECT 'Ana Martínez', '88880002', 'Manicure'
WHERE NOT EXISTS (SELECT 1 FROM empleados WHERE nombre = 'Ana Martínez');

INSERT INTO empleados (nombre, telefono, especialidad)
SELECT 'Laura Pérez', '88880003', 'Maquillaje y peinados'
WHERE NOT EXISTS (SELECT 1 FROM empleados WHERE nombre = 'Laura Pérez');
