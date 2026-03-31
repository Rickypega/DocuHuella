<?php
class Mascota {
    private $conexion;
    private $tabla = "Mascotas";

    // ATRIBUTOS
    public $id_mascota;
    public $nombre;
    public $especie;
    public $raza;
    public $sexo;
    public $color;
    public $edad;
    public $rasgos;
    public $peso;
    public $estado_esterilizacion;
    public $id_cuidador; 

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Registro de nueva mascota
     */
    public function registrarMascota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  SET Nombre = :nombre, Especie = :especie, Raza = :raza, Sexo = :sexo, 
                      Color = :color, Edad = :edad, Rasgos = :rasgos, Peso = :peso, 
                      Estado_Esterilizacion = :esteril, ID_Cuidador = :id_cuidador";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especie = htmlspecialchars(strip_tags($this->especie));
        $this->raza = htmlspecialchars(strip_tags($this->raza));
        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':especie', $this->especie);
        $stmt->bindParam(':raza', $this->raza);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':rasgos', $this->rasgos);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':esteril', $this->estado_esterilizacion);
        $stmt->bindParam(':id_cuidador', $this->id_cuidador);

        return $stmt->execute();
    }

    /**
     * Ver historial médico CON JOINS (Para que el cuidador entienda qué lee)
     */
    public function verHistorialMedico() {

    $query = "SELECT 
                e.ID_Expediente, 
                e.Fecha_Hora AS Fecha_Creacion,
                e.Motivo, 
                e.Diagnostico_Presuntivo, 
                e.Tratamiento_Recomendado,
                v.Nombre AS Nombre_Vet, 
                v.Apellido AS Apellido_Vet,
                c.Nombre_Sucursal AS Clinica
              FROM Expedientes e
              INNER JOIN Veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
              INNER JOIN Clinicas c ON v.ID_Clinica = c.ID_Clinica
              WHERE e.ID_Mascota = :id 
              ORDER BY e.Fecha_Hora DESC";

    $stmt = $this->conexion->prepare($query);
    $stmt->bindParam(':id', $this->id_mascota);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function actualizarDatos() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Peso = :peso, Edad = :edad, Rasgos = :rasgos, Estado_Esterilizacion = :esteril 
                  WHERE ID_Mascota = :id";
        
        $stmt = $this->conexion->prepare($query);

        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));

        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':rasgos', $this->rasgos);
        $stmt->bindParam(':esteril', $this->estado_esterilizacion);
        $stmt->bindParam(':id', $this->id_mascota);

        return $stmt->execute();
    }

}
?>