<?php
class administrador {
    private $conexion;
    private $tabla = "administrador";

    // ATRIBUTOS 
    public $id_admin;
    public $id_usuario; 
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
     * Gestionar veterinarios: Permite al admin ver a los empleados de SUS clínicas
     */
    public function gestionarUsuarios() {
        $query = "SELECT v.ID_Veterinario, u.ID_Usuario, u.Correo, u.Estado, 
                         v.Nombre, v.Apellido, v.Especialidad, c.Nombre_Sucursal 
                  FROM veterinarios v
                  INNER JOIN usuarios u ON v.ID_Usuario = u.ID_Usuario
                  INNER JOIN clinicas c ON v.ID_Clinica = c.ID_Clinica
                  WHERE c.ID_Admin = :id_admin";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generar Reportes: Estadísticas mensuales de CONSULTAS en SUS clínicas
     */
    public function generarReportes() {
        // Recorrido de la nueva arquitectura: Admin -> Clinica -> Expediente -> Consulta
        $query = "SELECT COUNT(cons.ID_Consulta) as total_consultas 
                  FROM consultas cons
                  INNER JOIN expedientes e ON cons.ID_Expediente = e.ID_Expediente
                  INNER JOIN clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE c.ID_Admin = :id_admin 
                  AND MONTH(cons.Fecha_Consulta) = MONTH(CURRENT_DATE())
                  AND YEAR(cons.Fecha_Consulta) = YEAR(CURRENT_DATE())";
        
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

