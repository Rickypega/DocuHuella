<?php
class Rol {
    private $conexion;
    private $tabla = "Roles";

    // Atributos 
    public $id_rol;
    public $nombre_rol;
    public $descripcion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Métodos (Acciones) 
    public function asignarPermisos() {
        // Lógica futura para definir qué hace cada rol
    }

    public function validarAcceso() {
        // Lógica para proteger las rutas del sistema
    }

    /**
     * LÓGICA DE INICIALIZACIÓN: 
     * Este método asegura que los roles siempre sean:
     * 1 = Administrador, 2 = Veterinario, 3 = Cuidador
     */
    public function crearRolesPredeterminados() {
        $roles = [
            ['id' => 1, 'nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema y gestión de usuarios.'],
            ['id' => 2, 'nombre' => 'Veterinario', 'descripcion' => 'Atención veterinaria y gestión de expedientes.'],
            ['id' => 3, 'nombre' => 'Cuidador', 'descripcion' => 'Tutor / dueño de las mascotas.']
        ];

        foreach ($roles as $r) {
            $query = "INSERT IGNORE INTO " . $this->tabla . " (id_rol, nombre_rol, descripcion) VALUES (:id, :nombre, :descripcion)";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':id', $r['id']);
            $stmt->bindParam(':nombre', $r['nombre']);
            $stmt->bindParam(':descripcion', $r['descripcion']);
            $stmt->execute();
        }
    }
}
?>