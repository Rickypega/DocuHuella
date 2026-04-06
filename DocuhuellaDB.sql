SET FOREIGN_KEY_CHECKS = 0; -- Desactiva revisiones para evitar errores de orden

DROP DATABASE IF EXISTS Docuhuella;
CREATE DATABASE Docuhuella;
USE Docuhuella;

-- ==========================================
-- 1. ROLES DEL SISTEMA
-- ==========================================
CREATE TABLE Roles (
    ID_Rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Rol VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255) NOT NULL
);

-- ==========================================
-- 2. USUARIOS (Información común para todos los perfiles)
-- ==========================================
CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Correo VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    ID_Rol INT NOT NULL,
    Estado VARCHAR(20) DEFAULT 'Activo', -- Activo, Suspendido, Eliminado
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Rol) REFERENCES Roles(ID_Rol)
);

-- ==========================================
-- 3. ADMINISTRADORES (Dueños de Franquicias)
-- ==========================================
CREATE TABLE Administrador (
    ID_Admin INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL, -- Enlace 1 a 1 con Usuarios
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Cedula VARCHAR(20) UNIQUE,
    Telefono VARCHAR(20),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

-- ==========================================
-- 4. CLÍNICAS (Sucursales Físicas)
-- ==========================================
CREATE TABLE Clinicas (
    ID_Clinica INT AUTO_INCREMENT PRIMARY KEY,
    ID_Admin INT NOT NULL, -- ¿Quién es el dueño de esta sucursal?
    Nombre_Sucursal VARCHAR(150) NOT NULL,
    Direccion VARCHAR(255) NOT NULL,
    Telefono VARCHAR(20),
    RNC VARCHAR(50), 
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado VARCHAR(20) DEFAULT 'Activa',
    FOREIGN KEY (ID_Admin) REFERENCES Administrador(ID_Admin) ON DELETE CASCADE
);

-- ==========================================
-- 5. VETERINARIOS
-- ==========================================
CREATE TABLE Veterinarios (
    ID_Veterinario INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    ID_Clinica INT NOT NULL, -- ¿En qué sucursal trabaja?
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Fecha_Nacimiento DATE NOT NULL,
    Cedula VARCHAR(20) UNIQUE,
    Sexo VARCHAR(10),
    Telefono VARCHAR(20),
    Especialidad VARCHAR(100),
    Direccion VARCHAR(150),
    Exequatur VARCHAR(50),
    Colegiatura VARCHAR(50),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica) ON DELETE CASCADE
);

-- ==========================================
-- 6. CUIDADORES (Dueños de Mascotas)
-- ==========================================
CREATE TABLE Cuidadores (
    ID_Cuidador INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Fecha_Nacimiento DATE NOT NULL,
    Cedula VARCHAR(20) UNIQUE,
    Sexo VARCHAR(10),
    Telefono VARCHAR(20),
    Direccion VARCHAR(150),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

-- ==========================================
-- 7. ESPECIES (Catálogo: Perro, Gato, Ave, etc.)
-- ==========================================
CREATE TABLE Especies (
    ID_Especie INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Especie VARCHAR(50) NOT NULL UNIQUE
);

-- ==========================================
-- 8. RAZAS (Depende de la Especie)
-- ==========================================
CREATE TABLE Razas (
    ID_Raza INT AUTO_INCREMENT PRIMARY KEY,
    ID_Especie INT NOT NULL,
    Nombre_Raza VARCHAR(50) NOT NULL,
    FOREIGN KEY (ID_Especie) REFERENCES Especies(ID_Especie) ON DELETE CASCADE
);

-- ==========================================
-- 9. MASCOTAS
-- ==========================================
CREATE TABLE Mascotas (
    ID_Mascota INT AUTO_INCREMENT PRIMARY KEY,
    ID_Cuidador INT NOT NULL, 
    ID_Especie INT NOT NULL,
    ID_Raza INT,
    Nombre VARCHAR(50) NOT NULL,
    Sexo VARCHAR(10),
    Color VARCHAR(50),
    Edad INT,
    Rasgos TEXT,
    Peso DECIMAL(5,2),
    Estado_Esterilizacion VARCHAR(50),
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Cuidador) REFERENCES Cuidadores(ID_Cuidador) ON DELETE CASCADE,
    FOREIGN KEY (ID_Especie) REFERENCES Especies(ID_Especie),
    FOREIGN KEY (ID_Raza) REFERENCES Razas(ID_Raza)
);

-- ==========================================
-- 10. EXPEDIENTES (Carpeta Maestra Única por Mascota)
-- ==========================================
CREATE TABLE Expedientes (
    ID_Expediente INT AUTO_INCREMENT PRIMARY KEY,
    ID_Mascota INT NOT NULL UNIQUE, -- Una mascota = Un solo expediente
    ID_Clinica INT NOT NULL,        -- Dónde se aperturó el expediente
    Fecha_Apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado_Expediente ENUM('Activo', 'Archivado', 'Fallecido') DEFAULT 'Activo',
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota) ON DELETE CASCADE,
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica) ON DELETE CASCADE
);

-- ==========================================
-- 11. CONSULTAS (Historial Médico Detallado)
-- ==========================================
CREATE TABLE Consultas (
    ID_Consulta INT AUTO_INCREMENT PRIMARY KEY,
    ID_Expediente INT NOT NULL,
    ID_Veterinario INT NOT NULL,
    Fecha_Consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
    Motivo_Consulta VARCHAR(255) NOT NULL,
    Sintomas TEXT NOT NULL,
    Peso_KG DECIMAL(5,2),
    Temperatura_C DECIMAL(4,2),
    Frecuencia_Cardiaca INT,
    Diagnostico TEXT,
    Tratamiento_Sugerido TEXT,
    Observaciones_Privadas TEXT,
    FOREIGN KEY (ID_Expediente) REFERENCES Expedientes(ID_Expediente) ON DELETE CASCADE,
    FOREIGN KEY (ID_Veterinario) REFERENCES Veterinarios(ID_Veterinario) ON DELETE CASCADE
);

-- ==========================================
-- 12. CITAS (Agenda)
-- ==========================================
CREATE TABLE Citas (
    ID_Cita INT AUTO_INCREMENT PRIMARY KEY,
    ID_Clinica INT NOT NULL,
    ID_Veterinario INT NOT NULL,
    ID_Mascota INT NOT NULL,
    Fecha_Cita DATE NOT NULL,
    Hora_Cita TIME NOT NULL,
    Motivo VARCHAR(150) NOT NULL,
    Estado ENUM('Pendiente', 'Confirmada', 'Completada', 'Cancelada') DEFAULT 'Pendiente',
    Notas TEXT NULL,
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica) ON DELETE CASCADE,
    FOREIGN KEY (ID_Veterinario) REFERENCES Veterinarios(ID_Veterinario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota) ON DELETE CASCADE
);

-- ==========================================
-- 13. NOTAS (Bloc Universal)
-- ==========================================
CREATE TABLE Notas (
    ID_Nota INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT NOT NULL, 
    ID_Mascota INT NULL, 
    Titulo VARCHAR(100) NOT NULL,
    Contenido TEXT(500) NOT NULL,
    Color_Etiqueta VARCHAR(20) DEFAULT '#f8f9fa', 
    Fecha_Creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Fecha_Actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota) ON DELETE SET NULL
);

-- ==========================================
-- 14. VACUNAS (Catálogo: Rabia, Parvovirus, etc.)
-- ==========================================
CREATE TABLE Vacunas (
    ID_Vacuna INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Vacuna VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Periodo_Refuerzo_Meses INT -- Para cálculos automáticos de próximas citas
);

-- ==========================================
-- 15. VACUNACIONES (Registro de aplicación)
-- ==========================================
CREATE TABLE Vacunaciones (
    ID_Vacunacion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Mascota INT NOT NULL,
    ID_Vacuna INT NOT NULL,
    ID_Veterinario INT NOT NULL,
    Fecha_Aplicacion DATE NOT NULL,
    Fecha_Refuerzo DATE,
    Lote_Vacuna VARCHAR(50), 
    Observaciones TEXT,
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota) ON DELETE CASCADE,
    FOREIGN KEY (ID_Vacuna) REFERENCES Vacunas(ID_Vacuna),
    FOREIGN KEY (ID_Veterinario) REFERENCES Veterinarios(ID_Veterinario)
);

SET FOREIGN_KEY_CHECKS = 1; -- Reactiva la seguridad