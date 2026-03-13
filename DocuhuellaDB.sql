CREATE DATABASE Docuhuella;
USE Docuhuella;

CREATE TABLE Roles (
    ID_Rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Rol VARCHAR(50) NOT NULL,
    Descripcion VARCHAR(255) NOT NULL
);



CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Correo VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    ID_Rol INT,
    FOREIGN KEY (ID_Rol) REFERENCES Roles(ID_Rol)
);


CREATE TABLE Veterinarios (
    ID_Veterinario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50),
    Apellido VARCHAR(50),
    Edad INT,
    Cedula VARCHAR(20),
    Sexo VARCHAR(10),
    Correo VARCHAR(100),
    Telefono VARCHAR(20),
    Especialidad VARCHAR(100),
    Direccion VARCHAR(150),
    Exequatur VARCHAR(50),
    Colegiatura VARCHAR(50),
    ID_Usuario INT UNIQUE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);


CREATE TABLE Cuidadores (
    ID_Cuidador INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50),
    Apellido VARCHAR(50),
    Edad INT,
    Cedula VARCHAR(20),
    Sexo VARCHAR(10),
    Telefono VARCHAR(20),
    Direccion VARCHAR(150),
    Correo VARCHAR(100),
    ID_Usuario INT UNIQUE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);

CREATE TABLE Administrador (
    ID_Admin INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50),
    Apellido VARCHAR(50),
    Cedula VARCHAR(20),
    Telefono VARCHAR(20),
    Correo VARCHAR(100),
    Clinica_Veterinaria VARCHAR(150),
    Direccion VARCHAR(150),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);


CREATE TABLE Mascotas (
    ID_Mascota INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50),
    Especie VARCHAR(50),
    Raza VARCHAR(50),
    Sexo VARCHAR(10),
    Color VARCHAR(50),
    Edad INT,
    Rasgos TEXT,
    Peso DECIMAL(5,2),
    Estado_Esterilizacion VARCHAR(50),
    ID_Cuidador INT,
    FOREIGN KEY (ID_Cuidador) REFERENCES Cuidadores(ID_Cuidador)
);


CREATE TABLE Expedientes (
    ID_Expediente INT AUTO_INCREMENT PRIMARY KEY,
    ID_Veterinario INT,
    ID_Mascota INT,
    Fecha_Hora DATETIME,
    Motivo TEXT,
    Diagnostico_Presuntivo TEXT,
    Tratamiento_Recomendado TEXT,
    FOREIGN KEY (ID_Veterinario) REFERENCES Veterinarios(ID_Veterinario),
    FOREIGN KEY (ID_Mascota) REFERENCES Mascotas(ID_Mascota)
);