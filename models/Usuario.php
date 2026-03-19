<?php
class Usuario {
    private $conexion;
    private $tabla = "Usuarios";

    // Atributos según el documento
    public $id_usuario;
    public $correo;
    public $contrasena;
    public $id_rol;
    public $estado; 
    public $fecha_registro; 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Acción: Iniciar Sesión
    public function login() {
        
        $query = "SELECT ID_Usuario, Correo, Contrasena, ID_Rol, Estado 
                  FROM " . $this->tabla . " 
                  WHERE Correo = :correo LIMIT 0,1";

        $stmt = $this->conexion->prepare($query);
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        return $stmt;
    }

    // Acción: Crear cuenta (necesaria para el registro )
    public function registrarUsuario() {
        $query = "INSERT INTO " . $this->tabla . " 
                  SET Correo = :correo, Contrasena = :pass, ID_Rol = :rol";

        $stmt = $this->conexion->prepare($query);

        // Si id_rol está vacío o no se ha definido, le asignamos 3 (Cuidador)
        if (empty($this->id_rol)) {
            $this->id_rol = 3; 
        }

        // Encriptar contraseña por seguridad
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':pass', $password_hash);
        $stmt->bindParam(':rol', $this->id_rol);
        
        try {
            if($stmt->execute()) {
                // Atrapamos el ID que se acaba de generar en la tabla Usuarios
                $this->id_usuario = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            
            if ($e->getCode() == 23000) { // Código de error para violación de clave única
                return 'correo_duplicado';
            } else {
                // Para otros errores
                return 'error_desconocido';
            }
        }
    }
}
?>