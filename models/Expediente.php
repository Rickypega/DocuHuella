<?php
class Expediente {
    private $conexion;
    private $tabla = "Expedientes";

    // ATRIBUTOS  
    public $id_expediente;
    public $id_veterinario; // FK hacia Veterinarios 
    public $id_mascota;     // FK hacia Mascotas 
    public $fecha_hora;
    public $motivo;
    public $diagnostico_presuntivo;
    public $tratamiento_recomendado;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // ACCIONES (MÉTODOS) 

    /**
     * Lógica para Guardar Consulta (Registrar el expediente) 
     */
    public function guardarConsulta() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Veterinario, ID_Mascota, Fecha_Hora, Motivo, Diagnostico_Presuntivo, Tratamiento_Recomendado) 
                  VALUES (:id_vet, :id_mas, :fecha, :motivo, :diag, :trata)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización para proteger los textos clínicos
        $this->motivo = htmlspecialchars(strip_tags($this->motivo));
        $this->diagnostico_presuntivo = htmlspecialchars(strip_tags($this->diagnostico_presuntivo));
        $this->tratamiento_recomendado = htmlspecialchars(strip_tags($this->tratamiento_recomendado));

        // Vinculación de parámetros para seguridad
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':id_mas', $this->id_mascota);
        $stmt->bindParam(':fecha', $this->fecha_hora);
        $stmt->bindParam(':motivo', $this->motivo);
        $stmt->bindParam(':diag', $this->diagnostico_presuntivo);
        $stmt->bindParam(':trata', $this->tratamiento_recomendado);

        return $stmt->execute();
    }

    /**
     * Lógica para Imprimir Receta
     */
    public function imprimirReceta() {
        return [
            'diagnostico' => $this->diagnostico_presuntivo,
            'tratamiento' => $this->tratamiento_recomendado,
            'fecha' => $this->fecha_hora
        ];
    }
}
?>