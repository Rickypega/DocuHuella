<?php
class Veterinario {
    private $conexion;
    private $tabla = "Veterinarios";

    // ATRIBUTOS 
    public $id_veterinario;
    public $nombre;
    public $apellido;
    public $fecha_nacimiento;
    public $cedula;
    public $sexo;
    public $correo;
    public $telefono;
    public $especialidad;
    public $direccion;
    public $exequatur; // Número de registro profesional 
    public $colegiatura;  //certificado de colegiatura que lo acredite como veterinario
    public $id_usuario; // FK hacia Usuarios 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS) 

    /**
     * Lógica para registrar el perfil del veterinario
     */
    public function registrarPerfil() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Nombre, Apellido, Fecha_Nacimiento, Cedula, Sexo, Correo, Telefono, Especialidad, Direccion, Exequatur, Colegiatura, ID_Usuario) 
                  VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula, :sexo, :correo, :telefono, :especialidad, :direccion, :exequatur, :colegiatura, :id_usuario)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización para seguridad
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->especialidad = htmlspecialchars(strip_tags($this->especialidad));
        $this->correo = htmlspecialchars(strip_tags($this->correo));

        // Vinculación de parámetros
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':especialidad', $this->especialidad);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':exequatur', $this->exequatur);
        $stmt->bindParam(':colegiatura', $this->colegiatura);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }

    /**
     * Lógica para crear un expediente clínico 
     * 
     */
    public function crearExpediente($id_mascota, $motivo, $diagnostico, $tratamiento) {
        // Esta lógica se conectará con el modelo Expediente.php
    }

    /**
     * Lógica para consultar el historial de una mascota 
     * 
     */
    public function consultarHistorial($id_mascota) {
        $query = "SELECT * FROM Expedientes WHERE ID_Mascota = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id_mascota);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lógica para emitir un diagnóstico 
     * 
     */
    public function emitirDiagnostico() {
        // Implementación futura según la interfaz del veterinario
    }
}
?>