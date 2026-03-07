<?php
class Administrador {
    private $conexion;
    private $tabla = "Administrador";

    // ATRIBUTOS EXACTOS DE TU DOCUMENTO [cite: 13, 36]
    public $id_admin;
    public $nombre;
    public $apellido;
    public $cedula;
    public $telefono;
    public $correo;
    public $clinica_veterinaria; // Campo: Clinica/Veterinaria [cite: 13]
    public $direccion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS) [cite: 37, 38]

    // Gestionar Usuarios: Permite al admin ver o suspender cuentas 
    public function gestionarUsuarios() {
        $query = "SELECT u.id_usuario, u.correo, r.nombre_rol 
                  FROM Usuarios u 
                  INNER JOIN Roles r ON u.id_rol = r.id_rol";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Generar Reportes: Estadísticas mensuales de consultas [cite: 38]
    public function generarReportes() {
        $query = "SELECT COUNT(*) as total_consultas 
                  FROM Expedientes 
                  WHERE MONTH(fecha_hora) = MONTH(CURRENT_DATE())";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Configurar Clínica: Actualizar datos de la institución [cite: 38]
    public function configurarClinica() {
        $query = "UPDATE " . $this->tabla . " 
                  SET clinica_veterinaria = :clinica, direccion = :dir, telefono = :tel 
                  WHERE id_admin = :id";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':clinica', $this->clinica_veterinaria);
        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':id', $this->id_admin);

        return $stmt->execute();
    }
}
?>