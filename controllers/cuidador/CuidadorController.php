<?php
require_once APP_PATH . '/config/auth_check.php';

class CuidadorController {

    public function index() {
        // 1. Instanciar la clase Database y obtener la conexión
        $database = new Database();
        $db = $database->getConnection();

        // 2. Realizar consultas COUNT(*) para obtener los totales
        $stmt_mascotas = $db->prepare("SELECT COUNT(*) FROM Mascotas WHERE ID_Cuidador = :id");
        $stmt_mascotas->execute([':id' => $_SESSION['id_perfil']]);
        $total_mascotas = $stmt_mascotas->fetchColumn();

        // 3. Incluir la vista
        include_once APP_PATH . '/views/cuidador/dashboard.php';
    }
}

// ========================================================================
// LÓGICA DE RUTEO SIMPLE
// ========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'ver') {
    $controlador = new CuidadorController();
    $controlador->index();
}
?>
