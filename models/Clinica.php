<?php
class Clinica {
    private $conexion;
    private $tabla = "Clinicas";

    // ATRIBUTOS
    public $id_clinica;
    public $id_admin;
    public $nombre_sucursal;
    public $direccion;
    public $telefono;
    public $rnc;
    public $estado;
    public $fecha_registro;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * Crear una nueva sucursal
     */
    public function registrarClinica() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (ID_Admin, Nombre_Sucursal, Direccion, Telefono, RNC) 
                  VALUES (:id_admin, :nombre, :dir, :tel, :rnc)";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza de datos
        $this->nombre_sucursal = htmlspecialchars(strip_tags($this->nombre_sucursal));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->rnc = htmlspecialchars(strip_tags($this->rnc));

        $stmt->bindParam(':id_admin', $this->id_admin);
        $stmt->bindParam(':nombre', $this->nombre_sucursal);
        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':rnc', $this->rnc);

        return $stmt->execute();
    }

    /**
     * Actualizar datos de la institución 
     */
    public function actualizarDatos() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Nombre_Sucursal = :nombre, Direccion = :dir, Telefono = :tel, RNC = :rnc 
                  WHERE ID_Clinica = :id";
        
        $stmt = $this->conexion->prepare($query);

        // Limpieza de datos
        $this->nombre_sucursal = htmlspecialchars(strip_tags($this->nombre_sucursal));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->rnc = htmlspecialchars(strip_tags($this->rnc));
            
        $stmt->bindParam(':nombre', $this->nombre_sucursal);
        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':rnc', $this->rnc);
        $stmt->bindParam(':id', $this->id_clinica);

        return $stmt->execute();
    }

    // ==========================================
    // MÉTODOS DE LECTURA Y ESTADO
    // ==========================================

    /**
     * Obtener todas las clínicas que le pertenecen a un Administrador
     */
    public function obtenerClinicasPorAdmin() {
        $query = "SELECT * FROM " . $this->tabla . " 
                  WHERE ID_Admin = :id_admin AND Estado = 'Activa'
                  ORDER BY Nombre_Sucursal ASC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_admin', $this->id_admin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Soft Delete: Cambiar el estado de la clínica (Ej. de Activa a Inactiva)
     */
    public function cambiarEstado() {
        $query = "UPDATE " . $this->tabla . " 
                  SET Estado = :estado 
                  WHERE ID_Clinica = :id";
        
        $stmt = $this->conexion->prepare($query);
        
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id', $this->id_clinica);
        
        return $stmt->execute();
    }
}
?>