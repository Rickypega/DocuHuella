<?php
class Cuidador {
    private $conexion;
    private $tabla = "Cuidadores";

    // ATRIBUTOS 
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

    /**
     * Lógica para registrarse (Crear perfil del cuidador)
     * Se usa inmediatamente después de crear el Usuario
     */
    public function registrarse() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Nombre, Apellido, Edad, Cedula, Sexo, Telefono, Direccion, Correo, ID_Usuario) 
                  VALUES (:nombre, :apellido, :edad, :cedula, :sexo, :telefono, :direccion, :correo, :id_usuario)";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza de datos (Sanitización)
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->correo = htmlspecialchars(strip_tags($this->correo));

        // Vinculación de parámetros
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

    /**
     * Lógica para editar perfil 
     */
    public function editarPerfil() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Nombre = :nombre, Apellido = :apellido, Telefono = :telefono, Direccion = :direccion 
                  WHERE ID_Cuidador = :id";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':id', $this->id_cuidador);

        return $stmt->execute();
    }

    /**
     * Lógica para ver mis mascotas (Relación 1:N) 
     */
    public function verMisMascotas() {
        // En el SQL la tabla es "Mascotas" y la FK es "ID_Cuidador" 
        $query = "SELECT * FROM Mascotas WHERE ID_Cuidador = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_cuidador);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>