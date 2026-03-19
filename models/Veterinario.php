<?php
class Veterinario {
    private $conexion;
    private $tabla = "Veterinarios";

    // ATRIBUTOS
    public $id_veterinario;
    public $id_usuario;
    public $id_clinica;
    public $nombre;
    public $apellido;
    public $fecha_nacimiento;
    public $cedula;
    public $sexo;
    public $telefono;
    public $especialidad;
    public $direccion;
    public $exequatur; 
    public $colegiatura; 

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Registro de perfil 
     */
    public function registrarPerfil() {
        $query = "INSERT INTO " . $this->tabla . " 
                  SET ID_Usuario = :id_usuario, ID_Clinica = :id_clinica, Nombre = :nombre, 
                      Apellido = :apellido, Fecha_Nacimiento = :fecha_nacimiento, Cedula = :cedula, 
                      Sexo = :sexo, Telefono = :telefono, Especialidad = :especialidad, 
                      Direccion = :direccion, Exequatur = :exequatur, Colegiatura = :colegiatura";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->especialidad = htmlspecialchars(strip_tags($this->especialidad));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->exequatur = htmlspecialchars(strip_tags($this->exequatur));
        $this->colegiatura = htmlspecialchars(strip_tags($this->colegiatura));

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':id_clinica', $this->id_clinica);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':especialidad', $this->especialidad);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':exequatur', $this->exequatur);
        $stmt->bindParam(':colegiatura', $this->colegiatura);

        return $stmt->execute();
    }

    /**
     * Consulta el historial clínico completo
     * Relaciona: Expediente -> Mascota -> Cuidador -> Veterinario -> Clínica
     */
    public function consultarHistorial($id_mascota) {
        $query = "SELECT 
                    e.ID_Expediente, 
                    e.Fecha_Creacion, 
                    e.Motivo, 
                    e.Diagnostico_Presuntivo, 
                    e.Tratamiento_Recomendado,
                    m.Nombre AS Nombre_Mascota,
                    m.Especie,
                    cui.Nombre AS Nombre_Dueño,
                    cui.Apellido AS Apellido_Dueño,
                    v.Nombre AS Nombre_Vet, 
                    v.Apellido AS Apellido_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM Expedientes e
                  INNER JOIN Mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN Cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN Veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id 
                  ORDER BY e.Fecha_Creacion DESC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id_mascota);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * BUSCAR POR CÉDULA
     * Trae todos los expedientes de todas las mascotas de un mismo dueño
     */
    public function buscarHistorialPorCedula($cedula_cliente) {
        $query = "SELECT 
                    e.ID_Expediente, 
                    e.Fecha_Creacion, 
                    e.Motivo, 
                    m.Nombre AS Nombre_Mascota,
                    cui.Nombre AS Nombre_Dueño,
                    v.Nombre AS Nombre_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM Expedientes e
                  INNER JOIN Mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN Cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN Veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE cui.Cedula = :cedula
                  ORDER BY e.Fecha_Creacion DESC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':cedula', $cedula_cliente);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>