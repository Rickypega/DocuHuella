<?php
session_start();
require_once '../../config/db.php';

class DashboardController {
    
    public function index() {
        // 1. SEGURIDAD: Validar que sea el SuperAdmin (Rol 4)
        if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 4) {
            header("Location: ../../views/login.php");
            exit();
        }

        // 2. LÓGICA: Conectar a la base de datos
        $database = new Database();
        $db = $database->getConnection();

        // Variables por defecto
        $nombre_rol = 'Super Administrador'; // Valor de respaldo
        $total_clinicas = 0;
        $total_veterinarios = 0;
        $total_cuidadores = 0;
        $total_mascotas = 0;
        $total_expedientes = 0;

       // 3. CONSULTAS: Estadísticas detalladas
        try {
            // Nombre del Rol (Dinámico)
            $stmt_rol = $db->prepare("SELECT Nombre_Rol FROM Roles WHERE ID_Rol = :id_rol");
            $stmt_rol->bindParam(':id_rol', $_SESSION['id_rol']);
            $stmt_rol->execute();
            $resultado_rol = $stmt_rol->fetchColumn();
            if ($resultado_rol) $nombre_rol = $resultado_rol;

            // --- CONTADORES ---
            
            // 1. Total de Clientes (Administradores registrados)
            $total_admins = $db->query("SELECT COUNT(*) FROM Administradores")->fetchColumn();
            
            // 2. Total de Sedes/Sucursales (Clínicas físicas)
            $total_clinicas = $db->query("SELECT COUNT(*) FROM Clinicas")->fetchColumn();
            
            // 3. Total de Empleados (Veterinarios en el sistema)
            $total_veterinarios = $db->query("SELECT COUNT(*) FROM Veterinarios")->fetchColumn();
            
            // 4. Total de Usuarios Finales (Dueños de mascotas)
            $total_cuidadores = $db->query("SELECT COUNT(*) FROM Cuidadores")->fetchColumn();
            
            // 5. El Corazón del Sistema (Pacientes y sus Historias)
            $total_mascotas = $db->query("SELECT COUNT(*) FROM Mascotas")->fetchColumn();
            $total_expedientes = $db->query("SELECT COUNT(*) FROM Expedientes")->fetchColumn();

        } catch (PDOException $e) {
            // Las variables se mantienen en 0 si hay error
        }

        // 4. PRESENTACIÓN: Enviar los datos a la Vista
        require_once '../../views/superadmin/dashboard.php';
    }
}

// ========================================================================
// LÓGICA DE RUTEO SIMPLE
// ========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'ver') {
    $controlador = new DashboardController();
    $controlador->index();
}
?>