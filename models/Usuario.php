<?php
class Usuario {
    private $conexion;
    private $tabla = "Usuarios";

    // Atributos exactos de tu tabla [cite: 13]
    public $id_usuario;
    public $correo;
    public $contraseña;
    public $id_rol;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // LÓGICA: Iniciar Sesión 
    public function iniciarSesion() {
        $query = "SELECT id_usuario, correo, contraseña, id_rol 
                  FROM " . $this->tabla . " 
                  WHERE correo = :correo LIMIT 0,1";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificamos si la contraseña coincide 
            if(password_verify($this->contraseña, $row['contraseña'])) {
                return $row; // Retorna los datos del usuario para la sesión
            }
        }
        return false; // Credenciales incorrectas
    }

    // LÓGICA: Cerrar Sesión 
    public function cerrarSesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return true;
    }
}
?>