<?php
class Cita {
    private $conexion;
    private $tabla = "Citas";

    // ATRIBUTOS
    public $id_cita;
    public $id_clinica;
    public $id_veterinario;
    public $id_mascota;
    public $fecha_cita;
    public $hora_cita;
    public $motivo;
    public $estado; // Pendiente, Confirmada, Completada, Cancelada
    public $notas;
    public $fecha_registro;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * 1. AGENDAR NUEVA CITA
     * Crea el registro en el calendario.
     */
    public function agendarCita() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Clinica, ID_Veterinario, ID_Mascota, Fecha_Cita, Hora_Cita, Motivo, Notas) 
                  VALUES (:id_clinica, :id_vet, :id_mascota, :fecha, :hora, :motivo, :notas)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización
        $this->motivo = htmlspecialchars(strip_tags($this->motivo));
        $this->notas = htmlspecialchars(strip_tags($this->notas));

        $stmt->bindParam(':id_clinica', $this->id_clinica);
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':id_mascota', $this->id_mascota);
        $stmt->bindParam(':fecha', $this->fecha_cita);
        $stmt->bindParam(':hora', $this->hora_cita);
        $stmt->bindParam(':motivo', $this->motivo);
        $stmt->bindParam(':notas', $this->notas);

        try {
            if($stmt->execute()) {
                $this->id_cita = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 2. OBTENER AGENDA DEL VETERINARIO
     * Trae las citas asignadas a un doctor específico.
     */
    public function obtenerAgendaVeterinario($id_vet) {
        $query = "SELECT c.ID_Cita, c.Fecha_Cita, c.Hora_Cita, c.Motivo, c.Estado, c.Notas,
                         m.Nombre AS Nombre_Mascota, 
                         esp.Nombre_Especie AS Especie,
                         cui.Nombre AS Nombre_Dueno, cui.Apellido AS Apellido_Dueno, cui.Telefono
                  FROM " . $this->tabla . " c
                  INNER JOIN Mascotas m ON c.ID_Mascota = m.ID_Mascota
                  INNER JOIN Especies esp ON m.ID_Especie = esp.ID_Especie
                  INNER JOIN Cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  WHERE c.ID_Veterinario = :id_vet 
                  AND c.Fecha_Cita >= CURRENT_DATE()
                  ORDER BY c.Fecha_Cita ASC, c.Hora_Cita ASC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_vet', $id_vet);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 3. OBTENER CITAS DEL CUIDADOR
     * Para que el dueño vea sus próximas visitas programadas.
     */
    public function obtenerCitasCuidador($id_cuidador) {
        $query = "SELECT c.ID_Cita, c.Fecha_Cita, c.Hora_Cita, c.Motivo, c.Estado,
                         m.Nombre AS Nombre_Mascota,
                         v.Nombre AS Nombre_Vet, v.Apellido AS Apellido_Vet,
                         cli.Nombre_Sucursal AS Clinica, cli.Direccion
                  FROM " . $this->tabla . " c
                  INNER JOIN Mascotas m ON c.ID_Mascota = m.ID_Mascota
                  INNER JOIN Veterinarios v ON c.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas cli ON c.ID_Clinica = cli.ID_Clinica
                  WHERE m.ID_Cuidador = :id_cuidador
                  ORDER BY c.Fecha_Cita DESC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_cuidador', $id_cuidador);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 4. ACTUALIZAR ESTADO DE LA CITA
     * Para marcarla como 'Confirmada', 'Cancelada' o 'Completada'.
     */
    public function cambiarEstado() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Estado = :estado 
                  WHERE ID_Cita = :id";
        
        $stmt = $this->conexion->prepare($query);
        
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id', $this->id_cita);
        
        return $stmt->execute();
    }
}
?>