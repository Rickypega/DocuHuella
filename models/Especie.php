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
}
?>
