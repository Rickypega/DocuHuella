<?php
class Especie {
    private $conexion;
    private $tabla = "especies";

    public $id_especie;
    public $nombre_especie;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Trae todas las especies para el <select>
     */
    public function obtenerTodas() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY Nombre_Especie ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Para que el veterinario pueda agregar "Animales Exóticos"
     */
    public function crearEspecie() {
        $query = "INSERT INTO " . $this->tabla . " (Nombre_Especie) VALUES (:nombre)";
        $stmt = $this->conexion->prepare($query);
        
        $this->nombre_especie = htmlspecialchars(strip_tags($this->nombre_especie));
        $stmt->bindParam(':nombre', $this->nombre_especie);
        
        return $stmt->execute();
    }

    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " SET Nombre_Especie = :nombre WHERE ID_Especie = :id";
        $stmt = $this->conexion->prepare($query);
        $this->nombre_especie = htmlspecialchars(strip_tags($this->nombre_especie));
        $stmt->bindParam(':nombre', $this->nombre_especie);
        $stmt->bindParam(':id', $this->id_especie);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE ID_Especie = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function tieneRelaciones($id) {
        // Verificar si tiene razas asociadas
        $q1 = "SELECT COUNT(*) FROM razas WHERE ID_Especie = :id";
        $s1 = $this->conexion->prepare($q1);
        $s1->bindParam(':id', $id);
        $s1->execute();
        if ($s1->fetchColumn() > 0) return true;

        // Verificar si hay mascotas de esta especie
        $q2 = "SELECT COUNT(*) FROM mascotas WHERE ID_Especie = :id";
        $s2 = $this->conexion->prepare($q2);
        $s2->bindParam(':id', $id);
        $s2->execute();
        return $s2->fetchColumn() > 0;
    }
}
?>
