<?php
class Cuidador {
    private $conexion;
    private $tabla = "Cuidadores";

    // ATRIBUTOS EXACTOS DE TU DOCUMENTO
    public $id_cuidador;
    public $nombre;
    public $apellido;
    public $edad;
    public $cedula;
    public $sexo;
    public $telefono;
    public $direccion;
    public $correo;
    public $id_usuario; // FK hacia Usuarios

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS)

    // Lógica para registrarse (Crear perfil del cuidador)
    public function registrarse() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, apellido, edad, cedula, sexo, telefono, direccion, correo, id_usuario) 
                  VALUES (:nombre, :apellido, :edad, :cedula, :sexo, :telefono, :direccion, :correo, :id_usuario)";
        
        $stmt = $this->conexion->prepare($query);

        // Vinculación de parámetros para seguridad
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }

    // Lógica para editar perfil
    public function editarPerfil() {
        $query = "UPDATE " . $this->tabla . " 
                  SET nombre = :nombre, apellido = :apellido, telefono = :telefono, direccion = :direccion 
                  WHERE id_cuidador = :id";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':id', $this->id_cuidador);

        return $stmt->execute();
    }

    // Lógica para ver mis mascotas (Relación 1:N)
    public function verMisMascotas() {
        $query = "SELECT * FROM Mascotas WHERE id_cuidador = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_cuidador);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>