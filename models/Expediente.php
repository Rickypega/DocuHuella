<?php
class Expediente {
    private $conexion;
    private $tabla = "Expedientes";

    // ATRIBUTOS 
    public $id_expediente;
    public $id_mascota; 
    public $id_veterinario; 
    public $id_clinica; // Para el sistema multi-sucursal
    public $fecha_creacion; 
    public $motivo;
    public $diagnostico_presuntivo;
    public $tratamiento_recomendado;
    public $estado_edicion; //  Abierto, Cerrado

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Registrar el expediente (Punto crítico del sistema)
     */
    public function guardarConsulta() {
        $query = "INSERT INTO " . $this->tabla . " 
                  SET ID_Mascota = :id_mas, ID_Veterinario = :id_vet, ID_Clinica = :id_cli, 
                      Motivo = :motivo, Diagnostico_Presuntivo = :diag, 
                      Tratamiento_Recomendado = :trata, Estado_Edicion = 'Abierto'";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización profunda
        $this->motivo = htmlspecialchars(strip_tags($this->motivo));
        $this->diagnostico_presuntivo = htmlspecialchars(strip_tags($this->diagnostico_presuntivo));
        $this->tratamiento_recomendado = htmlspecialchars(strip_tags($this->tratamiento_recomendado));

        $stmt->bindParam(':id_mas', $this->id_mascota);
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':id_cli', $this->id_clinica);
        $stmt->bindParam(':motivo', $this->motivo);
        $stmt->bindParam(':diag', $this->diagnostico_presuntivo);
        $stmt->bindParam(':trata', $this->tratamiento_recomendado);

        return $stmt->execute();
    }

    /**
     * Obtener datos para la Receta (Con nombres reales)
     */
    public function obtenerDatosReceta($id) {
        $query = "SELECT 
                    e.Fecha_Creacion, e.Diagnostico_Presuntivo, e.Tratamiento_Recomendado,
                    m.Nombre AS Mascota,
                    v.Nombre AS Veterinario, v.Apellido AS Apellido_Vet,
                    c.Nombre_Sucursal AS Clinica, c.Direccion AS Direccion_Clinica
                  FROM " . $this->tabla . " e
                  INNER JOIN Mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN Veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Expediente = :id";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>