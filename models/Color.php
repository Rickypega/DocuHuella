<?php
class Color {
    private $conn;
    private $table_name = "colores";

    public $id_color;
    public $nombre_color;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los colores para los selects
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Nombre_Color ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
