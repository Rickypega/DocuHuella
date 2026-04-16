<?php
class Mascota {
    private $conexion;
    private $tabla = "mascotas";

    // ATRIBUTOS
    public $id_mascota;
    public $id_cuidador;
    public $id_especie;
    public $id_raza;
    public $id_color;
    public $nombre;
    public $sexo;
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
                  (Nombre, ID_Especie, ID_Raza, Sexo, ID_Color, Edad, Rasgos, Peso, Estado_Esterilizacion, ID_Cuidador) 
                  VALUES (:nombre, :id_especie, :id_raza, :sexo, :id_color, :edad, :rasgos, :peso, :esteril, :id_cuidador)";
        
        $stmt = $this->conexion->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->rasgos = htmlspecialchars(strip_tags($this->rasgos));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':id_especie', $this->id_especie);
        $stmt->bindParam(':id_raza', $this->id_raza);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':id_color', $this->id_color);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':rasgos', $this->rasgos);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':esteril', $this->estado_esterilizacion);
        $stmt->bindParam(':id_cuidador', $this->id_cuidador);

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
        $query = "SELECT m.*, e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza, c.Nombre_Color AS Color
                  FROM " . $this->tabla . " m
                  LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                  LEFT JOIN colores c ON m.ID_Color = c.ID_Color
                  WHERE m.ID_Mascota = :id
                  LIMIT 1";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * VER HISTORIAL MÉDICO
     * Incluye nombre de la clínica mediante LEFT JOIN
     */
    public function verHistorialMedico() {
        $query = "SELECT 
                    c.ID_Consulta,
                    c.Fecha_Consulta,
                    c.Motivo_Consulta AS Motivo,
                    c.Diagnostico,
                    c.Tratamiento_Sugerido AS Tratamiento_Recomendado,
                    v.Nombre AS Nombre_Vet,
                    v.Apellido AS Apellido_Vet,
                    cli.Nombre_Sucursal AS Clinica
                  FROM consultas c
                  JOIN expedientes e ON c.ID_Expediente = e.ID_Expediente
                  JOIN veterinarios v ON c.ID_Veterinario = v.ID_Veterinario
                  JOIN clinicas cli ON v.ID_Clinica = cli.ID_Clinica
                  WHERE e.ID_Mascota = :id
                  ORDER BY c.Fecha_Consulta DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $this->id_mascota);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * OBTENER TODAS LAS MASCOTAS DE UN CUIDADOR
     */
    public function obtenerMascotasPorCuidador($id_cuidador) {
        $query = "SELECT m.*, e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza 
                  FROM " . $this->tabla . " m
                  LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                  WHERE m.ID_Cuidador = :id_c 
                  ORDER BY m.Nombre ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_c', $id_cuidador);
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

    /**
     * CATÁLOGOS
     */
    public function obtenerEspecies() {
        try {
            $query = "SELECT * FROM especies ORDER BY Nombre_Especie ASC";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) { return []; }
    }

    public function obtenerColores() {
        try {
            $query = "SELECT * FROM colores ORDER BY Nombre_Color ASC";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) { return []; }
    }

    /**
     * Obtiene info de contacto del cuidador para pre-llenar perfil
     */
    public function obtenerInfoCuidador($id_cuidador) {
        $query = "SELECT Telefono, Direccion FROM cuidadores WHERE ID_Cuidador = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([':id' => $id_cuidador]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

