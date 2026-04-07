<?php
class Vacuna {
    private $conexion;
    private $tabla = "Vacunas";

    public $id_vacuna;
    public $nombre_vacuna;
    public $descripcion;
    public $periodo_refuerzo_meses;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Trae todas las vacunas registradas en la clínica
     */
    public function leerTodas() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY Nombre_Vacuna ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Para cuando las farmacéuticas saquen una nueva vacuna
     */
    public function crearVacuna() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Nombre_Vacuna, Descripcion, Periodo_Refuerzo_Meses) 
                  VALUES (:nombre, :desc, :meses)";
                  
        $stmt = $this->conexion->prepare($query);
        
        $this->nombre_vacuna = htmlspecialchars(strip_tags($this->nombre_vacuna));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        
        $stmt->bindParam(':nombre', $this->nombre_vacuna);
        $stmt->bindParam(':desc', $this->descripcion);
        $stmt->bindParam(':meses', $this->periodo_refuerzo_meses);
        
        return $stmt->execute();
    }
}
?>