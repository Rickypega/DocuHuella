<?php
class Administrador {
    private $conexion;
    private $tabla = "Administrador";

    // ATRIBUTOS 
    public $id_admin;
    public $id_usuario; // La llave que lo une con Usuarios
    public $nombre;
    public $apellido;
    public $cedula;
    public $telefono;

    public function __construct($db) {
        $this->conexion = $db;
    }

    
    public function registrarAdministrador() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Usuario, Nombre, Apellido, Cedula, Telefono) 
                  VALUES (:id_usuario, :nombre, :apellido, :cedula, :telefono)";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza básica
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":cedula", $this->cedula);
        $stmt->bindParam(":telefono", $this->telefono);

        try {
            if($stmt->execute()) {
                // Atrapamos el ID generado para pasárselo al modelo Clinica
                $this->id_admin = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                return 'cedula_duplicada';
            }
            return 'error_desconocido';
        }
    }

    // ==========================================
    // MÉTODOS 
    // ==========================================

    /**
     * Gestionar Veterinarios: Permite al admin ver a los empleados de SUS clínicas
     */
    public function gestionarUsuarios() {
        // Hacemos JOIN con Clinicas para asegurarnos que solo vea a sus veterinarios
        $query = "SELECT u.Correo, v.Nombre, v.Apellido, c.Nombre_Sucursal 
                  FROM Veterinarios v
                  INNER JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario
                  INNER JOIN Clinicas c ON v.ID_Clinica = c.ID_Clinica
                  WHERE c.ID_Admin = :id_admin";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generar Reportes: Estadísticas mensuales de consultas en SUS clínicas
     */
    public function generarReportes() {
        // Contamos expedientes, pero solo los que ocurrieron en las clínicas de este Admin
        $query = "SELECT COUNT(*) as total_consultas 
                  FROM Expedientes e
                  INNER JOIN Clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE c.ID_Admin = :id_admin 
                  AND MONTH(e.Fecha_Creacion) = MONTH(CURRENT_DATE())";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar Datos Personales del Admin
     */
    public function actualizarPerfil() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Telefono = :tel 
                  WHERE ID_Admin = :id";
        
        $stmt = $this->conexion->prepare($query);

        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':id', $this->id_admin);

        return $stmt->execute();
    }
}
?>