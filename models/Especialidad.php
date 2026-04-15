<?php
class Especialidad {
    private $conn;
    private $table_name = "especialidades";

    public $id_especialidad;
    public $nombre_especialidad;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las especialidades para los selects
    public function obtenerTodas() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Nombre_Especialidad ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
