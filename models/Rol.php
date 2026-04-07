<?php
class Rol {
    private $conexion;
    private $tabla = "Roles";

    // Atributos
    public $id_rol;
    public $nombre_rol;
    public $descripcion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Obtener todos los roles de la base de datos
     */
    public function leerTodos() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY ID_Rol ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        // Estandarizado para devolver un array asociativo directo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>