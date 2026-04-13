<?php
class Veterinario {
    private $conexion;
    private $tabla = "veterinarios";

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
    public $id_especialidad; 
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
                  (ID_Usuario, ID_Clinica, Nombre, Apellido, Fecha_Nacimiento, Cedula, 
                   Sexo, Telefono, ID_Especialidad, Direccion, Exequatur, Colegiatura) 
                  VALUES (:id_usuario, :id_clinica, :nombre, :apellido, :fecha_nacimiento, :cedula, 
                          :sexo, :telefono, :id_especialidad, :direccion, :exequatur, :colegiatura)";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza de datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->id_especialidad = htmlspecialchars(strip_tags($this->id_especialidad));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->exequatur = htmlspecialchars(strip_tags($this->exequatur));
        $this->colegiatura = htmlspecialchars(strip_tags($this->colegiatura));

        // Vinculación de parámetros
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':id_clinica', $this->id_clinica);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':id_especialidad', $this->id_especialidad); 
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':exequatur', $this->exequatur);
        $stmt->bindParam(':colegiatura', $this->colegiatura);

        return $stmt->execute();
    }

    /**
     * Consulta el historial clínico completo 
     * Relaciona: Consulta -> Expediente -> Mascota -> Cuidador -> Veterinario -> Clínica
     */
    public function consultarHistorial($id_mascota) {
        $query = "SELECT 
                    cons.ID_Consulta, 
                    cons.Fecha_Consulta, 
                    cons.Motivo_Consulta AS Motivo, 
                    cons.Diagnostico, 
                    cons.Tratamiento_Sugerido,
                    m.Nombre AS Nombre_Mascota,
                    esp.Nombre_Especie AS Especie,
                    cui.Nombre AS Nombre_Dueno,
                    cui.Apellido AS Apellido_Dueno,
                    v.Nombre AS Nombre_Vet, 
                    v.Apellido AS Apellido_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM consultas cons
                  INNER JOIN expedientes e ON cons.ID_Expediente = e.ID_Expediente
                  INNER JOIN mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN especies esp ON m.ID_Especie = esp.ID_Especie
                  INNER JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN veterinarios v ON cons.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id 
                  ORDER BY cons.Fecha_Consulta DESC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id_mascota);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * BUSCAR POR CÉDULA DEL DUEÑO 
     * Trae todas las visitas médicas de todas las mascotas de un mismo dueño
     */
    public function buscarHistorialPorCedula($cedula_cliente) {
        $query = "SELECT 
                    cons.ID_Consulta, 
                    cons.Fecha_Consulta, 
                    cons.Motivo_Consulta AS Motivo, 
                    m.Nombre AS Nombre_Mascota,
                    cui.Nombre AS Nombre_Dueno,
                    v.Nombre AS Nombre_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM consultas cons
                  INNER JOIN expedientes e ON cons.ID_Expediente = e.ID_Expediente
                  INNER JOIN mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN veterinarios v ON cons.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE cui.Cedula = :cedula
                  ORDER BY cons.Fecha_Consulta DESC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':cedula', $cedula_cliente);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

