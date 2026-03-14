<?php
class Mascota {
    private $conexion;
    private $tabla = "Mascotas";

    // ATRIBUTOS (Coinciden con el documento y SQL)
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

    // ACCIONES (MÉTODOS)

    /**
     * Lógica para registrar una nueva mascota
     */
    public function registrarMascota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Nombre, Especie, Raza, Sexo, Color, Edad, Rasgos, Peso, Estado_Esterilizacion, ID_Cuidador) 
                  VALUES (:nombre, :especie, :raza, :sexo, :color, :edad, :rasgos, :peso, :esteril, :id_cuidador)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización para evitar scripts maliciosos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especie = htmlspecialchars(strip_tags($this->especie));
        $this->raza = htmlspecialchars(strip_tags($this->raza));
        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));

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

    /**
     * Lógica para actualizar los datos físicos de la mascota
     */
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

    /**
     * Lógica para ver el historial médico completo
     * Conecta con la tabla Expedientes
     */
    public function verHistorialMedico() {
        // En el SQL la tabla es "Expedientes" y la FK es "ID_Mascota"
        $query = "SELECT * FROM Expedientes WHERE ID_Mascota = :id ORDER BY Fecha_Hora DESC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>