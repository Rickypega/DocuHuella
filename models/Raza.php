<?php
class Raza {
    private $conexion;
    private $tabla = "Razas";

    public $id_raza;
    public $id_especie;
    public $nombre_raza;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Trae las razas vinculadas a una especie específica
     */
    public function leerPorEspecie($id_especie) {
        $query = "SELECT * FROM " . $this->tabla . " 
                  WHERE ID_Especie = :id_especie 
                  ORDER BY Nombre_Raza ASC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_especie', $id_especie);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Para agregar una nueva raza al catálogo
     */
    public function crearRaza() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Especie, Nombre_Raza) 
                  VALUES (:id_especie, :nombre)";
                  
        $stmt = $this->conexion->prepare($query);
        
        $this->nombre_raza = htmlspecialchars(strip_tags($this->nombre_raza));
        
        $stmt->bindParam(':id_especie', $this->id_especie);
        $stmt->bindParam(':nombre', $this->nombre_raza);
        
        return $stmt->execute();
    }
}
?>