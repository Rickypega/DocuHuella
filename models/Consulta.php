<?php
class Consulta
{
    private $conexion;
    private $tabla = "consultas";

    // ATRIBUTOS CLÍNICOS
    public $id_consulta;
    public $id_expediente;  // La carpeta a la que pertenece esta visita
    public $id_veterinario; // Qué doctor lo atendió HOY
    public $fecha_consulta;
    public $motivo_consulta;
    public $sintomas;
    public $peso_kg;
    public $temperatura_c;
    public $frecuencia_cardiaca;
    public $diagnostico;
    public $tratamiento_sugerido;
    public $observaciones_privadas; // notas solo para el staff

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    /**
     * REGISTRAR NUEVA VISITA MÉDICA
     */
    public function registrarConsulta()
    {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Expediente, ID_Veterinario, Motivo_Consulta, Sintomas, 
                   Peso_KG, Temperatura_C, Frecuencia_Cardiaca, Diagnostico, 
                   Tratamiento_Sugerido, Observaciones_Privadas) 
                  VALUES (:id_exp, :id_vet, :motivo, :sintomas, :peso, :temp, 
                          :frecuencia, :diag, :tratamiento, :obs)";

        $stmt = $this->conexion->prepare($query);

        // Sanitización de textos libres 
        $this->motivo_consulta = htmlspecialchars(strip_tags($this->motivo_consulta));
        $this->sintomas = htmlspecialchars(strip_tags($this->sintomas));
        $this->diagnostico = htmlspecialchars(strip_tags($this->diagnostico));
        $this->tratamiento_sugerido = htmlspecialchars(strip_tags($this->tratamiento_sugerido));
        $this->observaciones_privadas = htmlspecialchars(strip_tags($this->observaciones_privadas));

        $stmt->bindParam(':id_exp', $this->id_expediente);
        $stmt->bindParam(':id_vet', $this->id_veterinario);
        $stmt->bindParam(':motivo', $this->motivo_consulta);
        $stmt->bindParam(':sintomas', $this->sintomas);
        $stmt->bindParam(':peso', $this->peso_kg);
        $stmt->bindParam(':temp', $this->temperatura_c);
        $stmt->bindParam(':frecuencia', $this->frecuencia_cardiaca);
        $stmt->bindParam(':diag', $this->diagnostico);
        $stmt->bindParam(':tratamiento', $this->tratamiento_sugerido);
        $stmt->bindParam(':obs', $this->observaciones_privadas);

        try {
            if ($stmt->execute()) {
                $this->id_consulta = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * OBTENER EL HISTORIAL DE UN EXPEDIENTE
     * Trae todas las visitas médicas asociadas a la carpeta de la mascota, 
     * ordenadas desde la más reciente.
     */
    public function obtenerPorExpediente($id_expediente)
    {
        $query = "SELECT c.*, v.Nombre AS Nombre_Vet, v.Apellido AS Apellido_Vet 
                  FROM " . $this->tabla . " c
                  INNER JOIN veterinarios v ON c.ID_Veterinario = v.ID_Veterinario
                  WHERE c.ID_Expediente = :id_exp
                  ORDER BY c.Fecha_Consulta DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_exp', $id_expediente);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * OBTENER DATOS PARA IMPRIMIR RECETA
     * Une Consulta + Expediente + Mascota + Veterinario + Clínica
     */
    public function obtenerDatosReceta($id_consulta)
    {
        $query = "SELECT 
                    cons.Fecha_Consulta, 
                    cons.Peso_KG, 
                    cons.Temperatura_C, 
                    cons.Diagnostico, 
                    cons.Tratamiento_Sugerido,
                    m.Nombre AS Nombre_Mascota,
                    cui.Nombre AS Nombre_Dueno,
                    cui.Apellido AS Apellido_Dueno,
                    v.Nombre AS Nombre_Vet, 
                    v.Apellido AS Apellido_Vet, 
                    v.Exequatur,
                    c.Nombre_Sucursal AS Clinica, 
                    c.Direccion AS Direccion_Clinica, 
                    c.Telefono AS Telefono_Clinica
                  FROM " . $this->tabla . " cons
                  INNER JOIN expedientes e ON cons.ID_Expediente = e.ID_Expediente
                  INNER JOIN mascotas m ON e.ID_Mascota = m.ID_Mascota
                  INNER JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN veterinarios v ON cons.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN clinicas c ON e.ID_Clinica = c.ID_Clinica
                  WHERE cons.ID_Consulta = :id LIMIT 1";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id_consulta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    /**
     * OBTENER DETALLES COMPLETOS (5 PARTES)
     * Mascota, Consulta, Veterinario, Cuidador, Clínica
     */
    public function obtenerDetallesCompletos($id_consulta)
    {
        $query = "SELECT 
                    cons.*, 
                    m.Nombre AS Nombre_Mascota, m.Sexo AS Sexo_Mascota, m.Edad AS Edad_Mascota, m.ID_Cuidador,
                    e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza,
                    cui.Nombre AS Nombre_Cuidador, cui.Apellido AS Apellido_Cuidador, cui.Cedula AS Cedula_Cuidador, cui.Telefono AS Telefono_Cuidador,
                    v.Nombre AS Nombre_Vet, v.Apellido AS Apellido_Vet, v.Exequatur, v.Colegiatura,
                    cli.Nombre_Sucursal AS Clinica, cli.Direccion AS Direccion_Clinica, cli.Telefono AS Telefono_Clinica, cli.RNC AS RNC_Clinica
                  FROM " . $this->tabla . " cons
                  INNER JOIN expedientes exp ON cons.ID_Expediente = exp.ID_Expediente
                  INNER JOIN mascotas m ON exp.ID_Mascota = m.ID_Mascota
                  INNER JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  INNER JOIN veterinarios v ON cons.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN clinicas cli ON v.ID_Clinica = cli.ID_Clinica
                  LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                  WHERE cons.ID_Consulta = :id LIMIT 1";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id_consulta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>