<?php
class Veterinario {
    private $conexion;
    private $tabla = "Veterinarios";

    // ATRIBUTOS
    public $id_veterinario;
    public $nombre;
    public $apellido;
    public $edad;
    public $cedula;
    public $sexo;
    public $correo;
    public $telefono;
    public $especialidad;
    public $direccion;
    public $execuatur;
    public $colegiatura_colvet; // COLVET 
    public $id_usuario; // FK hacia Usuarios 

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS) 
    
    // Lógica para crear un expediente clínico 
    public function crearExpediente() {
        // Aquí irá la lógica para insertar en la tabla de Expedientes
    }

    // Lógica para consultar el historial de una mascota 
    public function consultarHistorial() {
        // Aquí la lógica para buscar registros médicos previos
    }

    // Lógica para emitir un diagnóstico 
    public function emitirDiagnostico() {
        // Aquí la lógica para validar y guardar diagnósticos
    }

    // Método extra para registrar el perfil del veterinario
    public function registrarPerfil() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, apellido, edad, cedula, sexo, correo, telefono, especialidad, direccion, execuatur, colegiatura_colvet, id_usuario) 
                  VALUES (:nombre, :apellido, :edad, :cedula, :sexo, :correo, :telefono, :especialidad, :direccion, :execuatur, :colvet, :id_usuario)";
        
        $stmt = $this->conexion->prepare($query);

        // Vinculación de todos los parámetros para seguridad
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':especialidad', $this->especialidad);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':execuatur', $this->execuatur);
        $stmt->bindParam(':colvet', $this->colegiatura_colvet);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }
}
?>