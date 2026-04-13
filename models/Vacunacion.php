<?php
class Vacunacion {
    private $conexion;
    private $tabla = "vacunaciones";

    // ATRIBUTOS
    public $id_vacunacion;
    public $id_mascota;
    public $id_vacuna;
    public $id_veterinario;
    public $fecha_aplicacion;
    public $fecha_refuerzo;
    public $lote_vacuna;
    public $observaciones;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * 1. REGISTRAR APLICACIÓN DE VACUNA
     */
    public function registrarVacunacion() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Mascota, ID_Vacuna, ID_Veterinario, Fecha_Aplicacion, Fecha_Refuerzo, Lote_Vacuna, Observaciones) 
                  VALUES (:id_mascota, :id_vacuna, :id_vet, :fecha_app, :fecha_ref, :lote, :obs)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización
        $this->lote_vacuna = htmlspecialchars(strip_tags($this->lote_vacuna));
        $this->observaciones = htmlspecialchars(strip_tags($this->observaciones));

        $stmt->bindParam(':id_mascota', $this->id_mascota);
        $stmt->bindParam(':id_vacuna', $this->id_vacuna);
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':fecha_app', $this->fecha_aplicacion);
        $stmt->bindParam(':fecha_ref', $this->fecha_refuerzo);
        $stmt->bindParam(':lote', $this->lote_vacuna);
        $stmt->bindParam(':obs', $this->observaciones);

        try {
            if($stmt->execute()) {
                $this->id_vacunacion = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 2. VER CARTILLA DE VACUNACIÓN DE UNA MASCOTA
     * Trae el historial de vacunas con los nombres reales de la vacuna y el doctor.
     */
    public function obtenerCartillaPorMascota($id_mascota) {
        $query = "SELECT 
                    vacu.ID_Vacunacion, 
                    vacu.Fecha_Aplicacion, 
                    vacu.Fecha_Refuerzo, 
                    vacu.Lote_Vacuna, 
                    vacu.Observaciones,
                    v.Nombre_Vacuna, 
                    v.Descripcion AS Descripcion_Vacuna,
                    vet.Nombre AS Nombre_Vet, 
                    vet.Apellido AS Apellido_Vet
                  FROM " . $this->tabla . " vacu
                  INNER JOIN vacunas v ON vacu.ID_Vacuna = v.ID_Vacuna
                  INNER JOIN veterinarios vet ON vacu.ID_Veterinario = vet.ID_Veterinario
                  WHERE vacu.ID_Mascota = :id_mascota
                  ORDER BY vacu.Fecha_Aplicacion DESC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_mascota', $id_mascota);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

