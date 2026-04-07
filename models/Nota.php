<?php
class Nota {
    private $conexion;
    private $tabla = "Notas";

    // ATRIBUTOS
    public $id_nota;
    public $id_usuario;
    public $id_mascota; // Puede ser NULL
    public $titulo;
    public $contenido;
    public $color_etiqueta; 
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * 1. CREAR UNA NUEVA NOTA
     */
    public function crearNota() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Usuario, ID_Mascota, Titulo, Contenido, Color_Etiqueta) 
                  VALUES (:id_usuario, :id_mascota, :titulo, :contenido, :color)";
        
        $stmt = $this->conexion->prepare($query);

        // Sanitización para evitar inyecciones en el contenido de la nota
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->color_etiqueta = htmlspecialchars(strip_tags($this->color_etiqueta));

        // Si ID_Mascota viene vacío, lo forzamos a NULL para no romper la llave foránea
        $id_mascota_valor = !empty($this->id_mascota) ? $this->id_mascota : null;

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':id_mascota', $id_mascota_valor);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':contenido', $this->contenido);
        $stmt->bindParam(':color', $this->color_etiqueta);

        try {
            if($stmt->execute()) {
                $this->id_nota = $this->conexion->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 2. OBTENER MIS NOTAS (Las del usuario que inició sesión)
     */
    public function obtenerNotasPorUsuario() {
        $query = "SELECT n.*, m.Nombre AS Nombre_Mascota 
                  FROM " . $this->tabla . " n
                  LEFT JOIN Mascotas m ON n.ID_Mascota = m.ID_Mascota
                  WHERE n.ID_Usuario = :id_usuario 
                  ORDER BY n.Fecha_Actualizacion DESC";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 3. ACTUALIZAR NOTA
     */
    public function actualizarNota() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Titulo = :titulo, Contenido = :contenido, Color_Etiqueta = :color 
                  WHERE ID_Nota = :id_nota AND ID_Usuario = :id_usuario";
        
        $stmt = $this->conexion->prepare($query);

        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->color_etiqueta = htmlspecialchars(strip_tags($this->color_etiqueta));

        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':contenido', $this->contenido);
        $stmt->bindParam(':color', $this->color_etiqueta);
        $stmt->bindParam(':id_nota', $this->id_nota);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }

    /**
     * 4. ELIMINAR NOTA
     */
    public function eliminarNota() {
        $query = "DELETE FROM " . $this->tabla . " 
                  WHERE ID_Nota = :id_nota AND ID_Usuario = :id_usuario";
                  
        $stmt = $this->conexion->prepare($query);
        
        $stmt->bindParam(':id_nota', $this->id_nota);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        
        return $stmt->execute();
    }
}
?>