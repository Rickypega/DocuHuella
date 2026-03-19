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
-- 2. USUARIOS (Información de inicio de sesión común para todos los perfiles)
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
-- 4.CLÍNICAS (Sucursales Físicas)
-- ==========================================
CREATE TABLE Clinicas (
    ID_Clinica INT AUTO_INCREMENT PRIMARY KEY,
    ID_Admin INT NOT NULL, -- ¿Quién es el dueño de esta sucursal?
    Nombre_Sucursal VARCHAR(150) NOT NULL,
    Direccion VARCHAR(255) NOT NULL,
    Telefono VARCHAR(20),
    RNC VARCHAR(50), -- Registro Nacional de Contribuyentes (para fines legales y fiscales)
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado VARCHAR(20) DEFAULT 'Activa',
    FOREIGN KEY (ID_Admin) REFERENCES Administrador(ID_Admin)
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
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica)
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
-- 7. MASCOTAS
-- ==========================================
CREATE TABLE Mascotas (
    ID_Mascota INT AUTO_INCREMENT PRIMARY KEY,
    ID_Cuidador INT NOT NULL, -- ¿De quién es el perrito/gatito?
    Nombre VARCHAR(50) NOT NULL,
    Especie VARCHAR(50) NOT NULL,
    Raza VARCHAR(50),
    Sexo VARCHAR(10),
    Color VARCHAR(50),
    Edad INT,
    Rasgos TEXT,
    Peso DECIMAL(5,2),
    Estado_Esterilizacion VARCHAR(50),
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Cuidador) REFERENCES Cuidadores(ID_Cuidador)
);

-- ==========================================
-- 8. EXPEDIENTES MÉDICOS 
-- ==========================================
CREATE TABLE Expedientes (
    ID_Expediente INT AUTO_INCREMENT PRIMARY KEY,
    ID_Mascota INT NOT NULL,      -- Paciente
    ID_Veterinario INT NOT NULL,  -- Médico tratante
    ID_Clinica INT NOT NULL,      -- Sucursal donde ocurrió 
    Fecha_Creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Motivo TEXT NOT NULL,
    Diagnostico_Presuntivo TEXT NOT NULL,
    Tratamiento_Recomendado TEXT NOT NULL,
    Estado_Edicion VARCHAR(20) DEFAULT 'Abierto', -- Abierto, Cerrado, En Revisión
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota),
    FOREIGN KEY (ID_Veterinario) REFERENCES Veterinarios(ID_Veterinario),
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica)
);


SET FOREIGN_KEY_CHECKS = 1; -- Reactiva la seguridad