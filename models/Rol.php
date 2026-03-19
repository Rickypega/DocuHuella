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
     * Obtener todos los roles para un formulario
     */
    public function leerTodos() {
        $query = "SELECT * FROM " . $this->tabla;
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>