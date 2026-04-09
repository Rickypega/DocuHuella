<?php

class Sucursal {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerSucursales() {
        $stmt = $this->conn->prepare("SELECT * FROM sucursales");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearSucursal($nombre, $direccion) {
        $stmt = $this->conn->prepare("INSERT INTO sucursales(nombre, direccion) VALUES (?, ?)");
        return $stmt->execute([$nombre, $direccion]);
    }
}
