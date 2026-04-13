<?php
require_once APP_PATH . '/config/auth_check.php';

class VeterinarioController {

    public function index() {
        // 1. Instanciar la clase Database y obtener la conexión
        $database = new Database();
        $db = $database->getConnection();

        // 2. Realizar consultas COUNT(*) para obtener los totales
        $total_mascotas = $db->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();
        $total_consultas = $db->query("SELECT COUNT(*) FROM expedientes")->fetchColumn();

        // 3. Incluir la vista
        include_once APP_PATH . '/views/veterinario/dashboard.php';
    }
}

// ========================================================================
// LÓGICA DE RUTEO SIMPLE
// ========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'ver') {
    $controlador = new VeterinarioController();
    $controlador->index();
}
?>

