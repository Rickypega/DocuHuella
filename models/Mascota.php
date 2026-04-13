<?php
class Mascota {
    private $conexion;
    private $tabla = "mascotas";

    // ATRIBUTOS
    public $id_mascota;
    public $id_cuidador;
    public $nombre;
    public $especie;
    public $raza;
    public $sexo;
    public $color;
    public $edad;
    public $rasgos;
    public $peso;
    public $estado_esterilizacion;
    public $imagen;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * REGISTRO DE NUEVA MASCOTA
     * Adaptado al dashboard actual
     */
    public function registrarMascota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (Nombre, Especie, Raza, Sexo, Color, Edad, Rasgos, Peso, Estado_Esterilizacion, ID_Cuidador, Imagen) 
                  VALUES (:nombre, :especie, :raza, :sexo, :color, :edad, :rasgos, :peso, :esteril, :id_cuidador, :imagen)";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especie = htmlspecialchars(strip_tags($this->especie));
        $this->raza = htmlspecialchars(strip_tags($this->raza));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->color = htmlspecialchars(strip_tags($this->color));
        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));
        $this->imagen = htmlspecialchars(strip_tags($this->imagen));

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
        $stmt->bindParam(':imagen', $this->imagen);

        try {
            if ($stmt->execute()) {
                $this->id_mascota = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * OBTENER PERFIL COMPLETO
     * Sin joins, usando la estructura actual
     */
    public function obtenerPerfilCompleto() {
        $query = "SELECT * 
                  FROM " . $this->tabla . "
                  WHERE ID_Mascota = :id
                  LIMIT 1";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * VER HISTORIAL MÉDICO
     * Adaptado a tu tabla expedientes real
     */
    public function verHistorialMedico() {
        $query = "SELECT 
                    e.ID_Expediente,
                    e.Fecha_Hora,
                    e.Motivo,
                    e.Diagnostico_Presuntivo,
                    e.Tratamiento_Recomendado,
                    v.Nombre AS Nombre_Vet,
                    v.Apellido AS Apellido_Vet
                  FROM expedientes e
                  LEFT JOIN veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  WHERE e.ID_Mascota = :id
                  ORDER BY e.Fecha_Hora DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ACTUALIZAR DATOS MÉTRICOS
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
}
?>
