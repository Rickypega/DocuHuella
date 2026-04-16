<?php
class Raza {
    private $conexion;
    private $tabla = "razas";

    public $id_raza;
    public $id_especie;
    public $nombre_raza;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Trae todas las razas con el nombre de su especie
     */
    public function obtenerTodas() {
        $query = "SELECT r.*, e.Nombre_Especie 
                  FROM " . $this->tabla . " r
                  JOIN especies e ON r.ID_Especie = e.ID_Especie
                  ORDER BY e.Nombre_Especie ASC, r.Nombre_Raza ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trae las razas vinculadas a una especie específica
     */
    public function obtenerPorEspecie($id_especie) {
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

    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " 
                  SET ID_Especie = :id_especie, 
                      Nombre_Raza = :nombre 
                  WHERE ID_Raza = :id";
                  
        $stmt = $this->conexion->prepare($query);
        
        $this->nombre_raza = htmlspecialchars(strip_tags($this->nombre_raza));
        
        $stmt->bindParam(':id_especie', $this->id_especie);
        $stmt->bindParam(':nombre', $this->nombre_raza);
        $stmt->bindParam(':id', $this->id_raza);
        
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE ID_Raza = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function tieneRelaciones($id) {
        $query = "SELECT COUNT(*) FROM mascotas WHERE ID_Raza = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
