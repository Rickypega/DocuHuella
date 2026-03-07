<?php
class Expediente {
    private $conexion;
    private $tabla = "Expedientes";

    // ATRIBUTOS EXACTOS DE TU DOCUMENTO 
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

    // Lógica para Guardar Consulta (Registrar el expediente) 
    public function guardarConsulta() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (id_veterinario, id_mascota, fecha_hora, motivo, diagnostico_presuntivo, tratamiento_recomendado) 
                  VALUES (:id_vet, :id_mas, :fecha, :motivo, :diag, :trata)";
        
        $stmt = $this->conexion->prepare($query);

        // Vinculación de parámetros para seguridad
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':id_mas', $this->id_mascota);
        $stmt->bindParam(':fecha', $this->fecha_hora);
        $stmt->bindParam(':motivo', $this->motivo);
        $stmt->bindParam(':diag', $this->diagnostico_presuntivo);
        $stmt->bindParam(':trata', $this->tratamiento_recomendado);

        return $stmt->execute();
    }

    // Lógica para Imprimir Receta (Simulación de salida de datos) 
    public function imprimirReceta() {
        // Esta lógica retornaría los datos formateados del tratamiento y diagnóstico 
        return [
            'diagnostico' => $this->diagnostico_presuntivo,
            'tratamiento' => $this->tratamiento_recomendado
        ];
    }
}
?>