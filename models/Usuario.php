<?php
class Usuario {
    private $conexion;
    private $tabla = "Usuarios";

    // Atributos
    public $id_usuario;
    public $correo;
    public $contrasena;
    public $id_rol;
    public $estado; 
    public $fecha_registro; 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ==========================================
    // Acción: Iniciar Sesión
    // ==========================================
    public function login() {
        $query = "SELECT ID_Usuario, Correo, Contrasena, ID_Rol, Estado 
                  FROM " . $this->tabla . " 
                  WHERE Correo = :correo LIMIT 1";

        $stmt = $this->conexion->prepare($query);
        
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        // Devolvemos directamente los datos del usuario (o false si no existe)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // Acción: Crear cuenta 
    // ==========================================
    public function registrarUsuario() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Correo, Contrasena, ID_Rol) 
                  VALUES (:correo, :pass, :rol)";

        $stmt = $this->conexion->prepare($query);

        // Si id_rol está vacío o no se ha definido, le asignamos 3 (Cuidador)
        if (empty($this->id_rol)) {
            $this->id_rol = 3; 
        }

        // Limpieza de correo
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        
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
            if ($e->getCode() == 23000) { // Violación de clave única
                return 'correo_duplicado';
            } else {
                return 'error_desconocido';
            }
        }
    }

    // ==========================================
    // MÉTODOS DE CONTROL
    // ==========================================

    /**
     * ACTUALIZAR ESTADO DE USUARIO (ACTIVO/INACTIVO)
     */
    public function cambiarEstado() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Estado = :estado 
                  WHERE ID_Usuario = :id";
        
        $stmt = $this->conexion->prepare($query);
        
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id', $this->id_usuario);
        
        return $stmt->execute();
    }
}
?>