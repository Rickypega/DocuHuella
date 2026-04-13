<?php
class Usuario {
    private $conexion;
    private $tabla = "usuarios";

    // Atributos
    public $id_usuario;
    public $correo;
    public $contrasena;
    public $id_rol;
    public $estado; 
    public $intentos_fallidos;
    public $fecha_registro; 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ==========================================
    // Acción: Iniciar Sesión
    // ==========================================
    public function login() {
        // CORRECCIÓN: Agregamos Intentos_Fallidos al SELECT
        $query = "SELECT ID_Usuario, Correo, Contrasena, ID_Rol, Estado, Intentos_Fallidos 
                  FROM " . $this->tabla . " 
                  WHERE Correo = :correo LIMIT 1";

        $stmt = $this->conexion->prepare($query);
        
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

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

        if (empty($this->id_rol)) {
            $this->id_rol = 3; 
        }

        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':pass', $password_hash);
        $stmt->bindParam(':rol', $this->id_rol);
        
        try {
            if($stmt->execute()) {
                $this->id_usuario = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                return 'correo_duplicado';
            } else {
                return 'error_desconocido';
            }
        }
    }

    // ==========================================
    // MÉTODOS DE CONTROL DE SEGURIDAD
    // ==========================================

    /**
     * ACTUALIZAR ESTADO DE USUARIO (Manual o Automático)
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

    /**
     * Incrementa el contador de fallos
     */
    public function registrarFallo($id) {
        $sql = "UPDATE " . $this->tabla . " 
                SET Intentos_Fallidos = Intentos_Fallidos + 1 
                WHERE ID_Usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Limpia el contador tras un login exitoso
     */
    public function resetearIntentos($id) {
        $sql = "UPDATE " . $this->tabla . " 
                SET Intentos_Fallidos = 0 
                WHERE ID_Usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
