<?php
class Mascota {
    private $conexion;
    private $tabla = "Mascotas";

    // ATRIBUTOS
    public $id_mascota;
    public $id_cuidador; 
    public $id_especie; 
    public $id_raza;    
    public $nombre;
    public $sexo;
    public $color;
    public $edad;
    public $rasgos;
    public $peso;
    public $estado_esterilizacion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * REGISTRO DE NUEVA MASCOTA
     */
    public function registrarMascota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Cuidador, ID_Especie, ID_Raza, Nombre, Sexo, Color, Edad, Rasgos, Peso, Estado_Esterilizacion) 
                  VALUES (:id_cuidador, :id_especie, :id_raza, :nombre, :sexo, :color, :edad, :rasgos, :peso, :esteril)";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza de datos de texto
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->color = htmlspecialchars(strip_tags($this->color));
        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));

        $stmt->bindParam(':id_cuidador', $this->id_cuidador);
        $stmt->bindParam(':id_especie', $this->id_especie);
        $stmt->bindParam(':id_raza', $this->id_raza);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':rasgos', $this->rasgos);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':esteril', $this->estado_esterilizacion);

        try {
            if($stmt->execute()) {
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
     * Trae los datos de la mascota incluyendo el texto real de su Especie y Raza.
     */
    public function obtenerPerfilCompleto() {
        $query = "SELECT m.*, e.Nombre_Especie, r.Nombre_Raza 
                  FROM " . $this->tabla . " m
                  INNER JOIN Especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN Razas r ON m.ID_Raza = r.ID_Raza
                  WHERE m.ID_Mascota = :id LIMIT 1";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * VER HISTORIAL MÉDICO 
     * Navega: Mascota -> Expediente -> Consultas
     */
    public function verHistorialMedico() {
        $query = "SELECT 
                    cons.ID_Consulta, 
                    cons.Fecha_Consulta,
                    cons.Motivo_Consulta AS Motivo, 
                    cons.Diagnostico, 
                    cons.Tratamiento_Sugerido,
                    v.Nombre AS Nombre_Vet, 
                    v.Apellido AS Apellido_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM Consultas cons
                  INNER JOIN Expedientes e ON cons.ID_Expediente = e.ID_Expediente
                  INNER JOIN Veterinarios v ON cons.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id 
                  ORDER BY cons.Fecha_Consulta DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ACTUALIZAR DATOS MÉTRICOS
     * Para cuando la mascota crece, sube de peso o es esterilizada.
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