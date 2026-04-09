<?php
class Cuidador {
    private $conexion;
    private $tabla = "Cuidadores";

    // ATRIBUTOS 
    public $id_cuidador;
    public $id_usuario;      
    public $nombre;
    public $apellido;
    public $fecha_nacimiento;
    public $cedula;
    public $sexo;
    public $telefono;
    public $direccion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * OBTENER PERFIL POR USUARIO
     */
    public function obtenerCuidadorPorUsuario() {
        $query = "SELECT * FROM " . $this->tabla . " WHERE ID_Usuario = :id_usuario LIMIT 1";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * OBTENER POR CÉDULA (Útil para el flujo de Veterinaria)
     */
    public function obtenerPorCedula($cedula) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE Cedula = :cedula LIMIT 1";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * REGISTRAR PERFIL
     */
    public function registrarse() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Usuario, Nombre, Apellido, Fecha_Nacimiento, Cedula, Sexo, Telefono, Direccion) 
                  VALUES (:id_usuario, :nombre, :apellido, :fecha_nacimiento, :cedula, :sexo, :telefono, :direccion)";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);

        try {
            if($stmt->execute()) {
                $this->id_cuidador = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                return 'cedula_duplicada';
            }
            return false;
        }
    }

    /**
     * ACTUALIZAR PERFIL
     */
    public function editarPerfil() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Nombre = :nombre, Apellido = :apellido, 
                      Telefono = :telefono, Direccion = :direccion 
                  WHERE ID_Cuidador = :id";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':id', $this->id_cuidador);

        return $stmt->execute();
    }

    /**
     * OBTENER MIS MASCOTAS (Incluyendo Especie, Raza y Color)
     */
    public function obtenerMisMascotas() {
        $query = "SELECT m.*, e.Nombre_Especie, r.Nombre_Raza, col.Nombre_Color 
                  FROM Mascotas m
                  INNER JOIN Especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN Razas r ON m.ID_Raza = r.ID_Raza
                  LEFT JOIN Colores col ON m.ID_Color = col.ID_Color
                  WHERE m.ID_Cuidador = :id
                  ORDER BY m.Fecha_Registro DESC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_cuidador);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}