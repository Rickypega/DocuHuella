<?php
class Expediente {
    private $conexion;
    private $tabla = "expedientes";

    // ATRIBUTOS 
    public $id_expediente;
    public $id_mascota; 
    public $id_clinica; 
    public $fecha_apertura; 
    public $estado_expediente; // Activo, Archivado, Fallecido

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * CREAR LA CARPETA MAESTRA
     * Esto solo debe ejecutarse UNA vez en toda la vida de la mascota.
     */
    public function crearExpediente() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Mascota, ID_Clinica) 
                  VALUES (:id_mascota, :id_clinica)";
        
        $stmt = $this->conexion->prepare($query);

        $stmt->bindParam(':id_mascota', $this->id_mascota);
        $stmt->bindParam(':id_clinica', $this->id_clinica);

        try {
            if($stmt->execute()) {
                $this->id_expediente = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            // Error 23000 es duplicidad. Significa que la mascota ya tiene expediente.
            if ($e->getCode() == 23000) { 
                return 'expediente_existente';
            }
            return false;
        }
    }

    /**
     * OBTENER EL EXPEDIENTE DE UNA MASCOTA
     * Busca la carpeta para poder mostrarla en el perfil del animal.
     */
    public function obtenerPorMascota($id_mascota) {
        $query = "SELECT e.*, c.Nombre_Sucursal AS Clinica_Origen 
                  FROM " . $this->tabla . " e
                  INNER JOIN clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id_mascota LIMIT 1";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_mascota', $id_mascota);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * SOFT DELETE / ACTUALIZACIÓN DE ESTADO
     * Permite archivar el expediente o marcar al paciente como fallecido 
     * sin borrar su historial médico legal.
     */
    public function cambiarEstado() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Estado_Expediente = :estado 
                  WHERE ID_Expediente = :id";
        
        $stmt = $this->conexion->prepare($query);
        
        // Sanitizamos el estado por si acaso (ENUM en DB)
        $this->estado_expediente = htmlspecialchars(strip_tags($this->estado_expediente));

        $stmt->bindParam(':estado', $this->estado_expediente);
        $stmt->bindParam(':id', $this->id_expediente);

        return $stmt->execute();
    }
}
?>

