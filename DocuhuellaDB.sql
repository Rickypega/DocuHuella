SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS Docuhuella;
CREATE DATABASE Docuhuella;
USE Docuhuella;

-- ==========================================
-- I. TABLAS MAESTRAS (CATÁLOGOS)
-- ==========================================

-- 1. TABLA: Roles (Admin, Veterinario, Cuidador)
CREATE TABLE Roles (
    ID_Rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Rol VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255) NOT NULL
);

-- 2. TABLA: Especialidades (Medicina General, Dermatología, Cirugía, etc.)
CREATE TABLE Especialidades (
    ID_Especialidad INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Especialidad VARCHAR(100) NOT NULL UNIQUE
);

-- 3. TABLA: Colores (colores para la mascota)
CREATE TABLE Colores (
    ID_Color INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Color VARCHAR(50) NOT NULL UNIQUE
);

-- 4. TABLA: Especies (Perro, Gato, etc.)
CREATE TABLE Especies (
    ID_Especie INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Especie VARCHAR(50) NOT NULL UNIQUE
);

-- 5. TABLA: Razas (Relacionada con Especie)
CREATE TABLE Razas (
    ID_Raza INT AUTO_INCREMENT PRIMARY KEY,
    ID_Especie INT NOT NULL,
    Nombre_Raza VARCHAR(50) NOT NULL,
    FOREIGN KEY (ID_Especie) REFERENCES Especies(ID_Especie) ON DELETE CASCADE
);

-- ==========================================
-- II. GESTIÓN DE USUARIOS Y ACCESOS
-- ==========================================

-- 6. TABLA: Usuarios (Credenciales de login)
CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Correo VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    ID_Rol INT NOT NULL,
    Estado VARCHAR(20) DEFAULT 'Activo',
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Rol) REFERENCES Roles(ID_Rol)
);

-- 7. TABLA: Administrador (Dueño de la red de clínicas)
CREATE TABLE Administrador (
    ID_Admin INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Cedula VARCHAR(20) UNIQUE,
    Telefono VARCHAR(20),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

-- 8. TABLA: Clinicas (Sucursales)
CREATE TABLE Clinicas (
    ID_Clinica INT AUTO_INCREMENT PRIMARY KEY,
    ID_Admin INT NOT NULL,
    Nombre_Sucursal VARCHAR(150) NOT NULL,
    Direccion VARCHAR(255) NOT NULL,
    Telefono VARCHAR(20),
    RNC VARCHAR(50), 
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado VARCHAR(20) DEFAULT 'Activa',
    FOREIGN KEY (ID_Admin) REFERENCES Administrador(ID_Admin) ON DELETE CASCADE
);

-- 9. TABLA: Veterinarios (Perfil médico)
CREATE TABLE Veterinarios (
    ID_Veterinario INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    ID_Clinica INT NOT NULL,
    ID_Especialidad INT, 
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Fecha_Nacimiento DATE NOT NULL,
    Cedula VARCHAR(20) UNIQUE,
    Sexo VARCHAR(10),
    Telefono VARCHAR(20),
    Direccion VARCHAR(150),
    Exequatur VARCHAR(50),
    Colegiatura VARCHAR(50),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica) ON DELETE CASCADE,
    FOREIGN KEY (ID_Especialidad) REFERENCES Especialidades(ID_Especialidad) ON DELETE SET NULL
);

-- ==========================================
-- III. CLIENTES Y PACIENTES
-- ==========================================

-- 10. TABLA: Cuidadores (Dueños de mascotas)
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

-- 11. TABLA: Mascotas
CREATE TABLE Mascotas (
    ID_Mascota INT AUTO_INCREMENT PRIMARY KEY,
    ID_Cuidador INT NOT NULL, 
    ID_Especie INT NOT NULL,
    ID_Raza INT,
    ID_Color INT,
    Nombre VARCHAR(50) NOT NULL,
    Sexo VARCHAR(10),
    Edad INT,
    Rasgos TEXT,
    Peso DECIMAL(5,2),
    Estado_Esterilizacion VARCHAR(50),
    Fecha_Registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Cuidador) REFERENCES Cuidadores(ID_Cuidador) ON DELETE CASCADE,
    FOREIGN KEY (ID_Especie) REFERENCES Especies(ID_Especie),
    FOREIGN KEY (ID_Raza) REFERENCES Razas(ID_Raza),
    FOREIGN KEY (ID_Color) REFERENCES Colores(ID_Color) ON DELETE SET NULL
);

-- ==========================================
-- IV. HISTORIAL CLÍNICO Y OPERACIONES
-- ==========================================

-- 12. TABLA: Expedientes (El folder de la mascota)
CREATE TABLE Expedientes (
    ID_Expediente INT AUTO_INCREMENT PRIMARY KEY,
    ID_Mascota INT NOT NULL UNIQUE,
    ID_Clinica INT NOT NULL,
    Fecha_Apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estado_Expediente ENUM('Activo', 'Archivado', 'Fallecido') DEFAULT 'Activo',
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota) ON DELETE CASCADE,
    FOREIGN KEY (ID_Clinica) REFERENCES Clinicas(ID_Clinica) ON DELETE CASCADE
);

-- 13. TABLA: Consultas (Registro médico por visita)
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

-- 14. TABLA: Citas (Agenda)
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

-- 15. TABLA: Vacunas (Catálogo)
CREATE TABLE Vacunas (
    ID_Vacuna INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Vacuna VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Periodo_Refuerzo_Meses INT
);

-- 16. TABLA: Vacunaciones (Control de dosis)
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

-- 17. TABLA: Notas (Bloc de notas del sistema)
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

SET FOREIGN_KEY_CHECKS = 1;