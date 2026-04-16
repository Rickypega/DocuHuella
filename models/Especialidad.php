<?php
class Especialidad {
    private $conexion;
    private $tabla = "especialidades";

    public $id_especialidad;
    public $nombre_especialidad;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Obtener todas las especialidades para los selects
    public function obtenerTodas() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY Nombre_Especialidad ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " (Nombre_Especialidad) VALUES (:nombre)";
        $stmt = $this->conexion->prepare($query);
        $this->nombre_especialidad = htmlspecialchars(strip_tags($this->nombre_especialidad));
        $stmt->bindParam(':nombre', $this->nombre_especialidad);
        return $stmt->execute();
    }

    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " SET Nombre_Especialidad = :nombre WHERE ID_Especialidad = :id";
        $stmt = $this->conexion->prepare($query);
        $this->nombre_especialidad = htmlspecialchars(strip_tags($this->nombre_especialidad));
        $stmt->bindParam(':nombre', $this->nombre_especialidad);
        $stmt->bindParam(':id', $this->id_especialidad);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE ID_Especialidad = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function tieneRelaciones($id) {
        $query = "SELECT COUNT(*) FROM veterinarios WHERE ID_Especialidad = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
