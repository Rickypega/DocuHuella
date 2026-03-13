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
    public $id_cuidador; // FK hacia Cuidadores

    public function __construct($db) {
        $this->conexion = $db;
    }
// ajaggadloghagihjna
    // ACCIONES (MÉTODOS)

    // Lógica para registrar una nueva mascota
    public function registrarMascota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, especie, raza, sexo, color, edad, rasgos, peso, estado_esterilizacion, id_cuidador) 
                  VALUES (:nombre, :especie, :raza, :sexo, :color, :edad, :rasgos, :peso, :esteril, :id_cuidador)";
        
        $stmt = $this->conexion->prepare($query);

        // Vinculación segura de parámetros
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

    // Lógica para actualizar los datos físicos de la mascota
    public function actualizarDatos() {
        $query = "UPDATE " . $this->tabla . " 
                  SET peso = :peso, edad = :edad, rasgos = :rasgos, estado_esterilizacion = :esteril 
                  WHERE id_mascota = :id";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':rasgos', $this->rasgos);
        $stmt->bindParam(':esteril', $this->estado_esterilizacion);
        $stmt->bindParam(':id', $this->id_mascota);

        return $stmt->execute();
    }

    // Lógica para ver el historial médico completo
    public function verHistorialMedico() {
        $query = "SELECT * FROM Expedientes WHERE id_mascota = :id ORDER BY fecha_hora DESC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>