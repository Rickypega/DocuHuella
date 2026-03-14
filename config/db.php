<?php
class Database {
    // Configuración para XAMPP (Local)
    private $host = "localhost";
    private $db_name = "Docuhuella";
    private $username = "root";
    private $password = ""; 
    public $conn;

    // Método para obtener la conexión
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Habilitar errores para desarrollo
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>