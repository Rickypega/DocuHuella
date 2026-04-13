<?php
require_once 'config/auth_check.php';

class DashboardController {
    
    public function index() {
        // 1. Instanciar la clase Database y obtener la conexión
        $database = new Database();
        $db = $database->getConnection();

        // 2. Realizar consultas COUNT(*) para obtener los totales
        $total_admins = $db->query("SELECT COUNT(*) FROM administrador")->fetchColumn();
        $total_clinicas = $db->query("SELECT COUNT(*) FROM clinicas")->fetchColumn();
        $total_veterinarios = $db->query("SELECT COUNT(*) FROM veterinarios")->fetchColumn();
        $total_expedientes = $db->query("SELECT COUNT(*) FROM expedientes")->fetchColumn();
        $total_cuidadores = $db->query("SELECT COUNT(*) FROM cuidadores")->fetchColumn();
        $total_mascotas = $db->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();

        // 3. Incluir la vista
        include_once APP_PATH . '/views/superadmin/dashboard.php';
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
