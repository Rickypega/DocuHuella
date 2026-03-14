<?php
class Administrador {
    private $conexion;
    private $tabla = "Administrador";

    // ATRIBUTOS 
    public $id_admin;
    public $nombre;
    public $apellido;
    public $cedula;
    public $telefono;
    public $correo;
    public $clinica_veterinaria; // Nombre: Clinica/Veterinaria 
    public $direccion;
    public $id_usuario; // Relación sugerida en el documento 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS) 

    /**
     * Gestionar Usuarios: Permite al admin ver todas las cuentas y sus roles 
     */
    public function gestionarUsuarios() {
       
        $query = "SELECT u.ID_Usuario, u.Correo, r.Nombre_Rol 
                  FROM Usuarios u 
                  INNER JOIN Roles r ON u.ID_Rol = r.ID_Rol";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generar Reportes: Estadísticas mensuales de consultas 
     */
    public function generarReportes() {
        // Ajustado a la columna Fecha_Hora de tu SQL
        $query = "SELECT COUNT(*) as total_consultas 
                  FROM Expedientes 
                  WHERE MONTH(Fecha_Hora) = MONTH(CURRENT_DATE())";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Configurar Clínica: Actualizar datos de la institución
     */
    public function configurarClinica() {
        // Ajustado a las columnas Clinica_Veterinaria e ID_Admin de tu SQL
        $query = "UPDATE " . $this->tabla . " 
                  SET Direccion = :dir, Telefono = :tel 
                  WHERE ID_Admin = :id";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización para seguridad
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':id', $this->id_admin);

        return $stmt->execute();
    }
}
?>