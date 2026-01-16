-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS rja_radio;
USE rja_radio;

-- Tabla de roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    nivel_acceso INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    rol_id INT,
    activo BOOLEAN DEFAULT true,
    ultimo_login TIMESTAMP,
    intentos_fallidos INT DEFAULT 0,
    fecha_bloqueo TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de directores
CREATE TABLE directores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    direccion TEXT,
    cargo VARCHAR(100),
    departamento VARCHAR(100),
    fecha_ingreso DATE,
    hora_entrada TIME,
    hora_salida TIME,
    periodo_entrada ENUM('AM', 'PM') NOT NULL,
    periodo_salida ENUM('AM', 'PM') NOT NULL,
    dias_laborables VARCHAR(255),
    estado_civil VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de locutores
CREATE TABLE locutores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    direccion TEXT,
    programa VARCHAR(100),
    descripcion_programa TEXT,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    periodo_inicio ENUM('AM', 'PM') NOT NULL,
    periodo_fin ENUM('AM', 'PM') NOT NULL,
    dias_trabajo VARCHAR(255),
    estado_civil VARCHAR(20),
    experiencia_anos INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de moderadores
CREATE TABLE moderadores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    direccion TEXT,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    periodo_inicio ENUM('AM', 'PM') NOT NULL,
    periodo_fin ENUM('AM', 'PM') NOT NULL,
    dias_moderacion VARCHAR(255),
    area_moderacion VARCHAR(100),
    nivel_experiencia VARCHAR(50),
    estado_civil VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de permisos
CREATE TABLE permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50),
    nivel_requerido INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de relación roles_permisos
CREATE TABLE roles_permisos (
    rol_id INT,
    permiso_id INT,
    asignado_por INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rol_id, permiso_id),
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (permiso_id) REFERENCES permisos(id),
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id)
);

-- Tabla de auditoría
CREATE TABLE auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(50) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_id INT,
    detalles TEXT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de sesiones
CREATE TABLE sesiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    token VARCHAR(255) NOT NULL,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    activa BOOLEAN DEFAULT true,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);