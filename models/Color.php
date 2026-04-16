<?php
class Color {
    private $conexion;
    private $tabla = "colores";

    public $id_color;
    public $nombre_color;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Obtener todos los colores para los selects
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY Nombre_Color ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " (Nombre_Color) VALUES (:nombre)";
        $stmt = $this->conexion->prepare($query);
        $this->nombre_color = htmlspecialchars(strip_tags($this->nombre_color));
        $stmt->bindParam(':nombre', $this->nombre_color);
        return $stmt->execute();
    }

    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " SET Nombre_Color = :nombre WHERE ID_Color = :id";
        $stmt = $this->conexion->prepare($query);
        $this->nombre_color = htmlspecialchars(strip_tags($this->nombre_color));
        $stmt->bindParam(':nombre', $this->nombre_color);
        $stmt->bindParam(':id', $this->id_color);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE ID_Color = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function tieneRelaciones($id) {
        $query = "SELECT COUNT(*) FROM mascotas WHERE ID_Color = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
