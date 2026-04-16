<?php
require_once APP_PATH . '/config/auth_check.php';
require_once APP_PATH . '/models/Cita.php';

class CuidadorController {

    public function index() {
        // 1. Instanciar la clase Database y obtener la conexión
        $database = new Database();
        $db = $database->getConnection();

        // 2. Realizar consultas COUNT(*) para obtener los totales
        $stmt_mascotas = $db->prepare("SELECT COUNT(*) FROM mascotas WHERE ID_Cuidador = :id");
        $stmt_mascotas->execute([':id' => $_SESSION['id_perfil']]);
        $total_mascotas = $stmt_mascotas->fetchColumn();

        // 3. Obtener citas para el calendario
        $citaModel = new Cita($db);
        $citas = $citaModel->obtenerCitasCuidador($_SESSION['id_perfil']);

        // 4. Obtener Catálogos y Datos de Perfil para Modales
        require_once APP_PATH . '/models/Mascota.php';
        $mascotaModel = new Mascota($db);
        $especies = $mascotaModel->obtenerEspecies();
        $colores  = $mascotaModel->obtenerColores();
        $perfil_info = $mascotaModel->obtenerInfoCuidador($_SESSION['id_perfil']);
        
        $especies = is_array($especies) ? $especies : [];
        $colores  = is_array($colores) ? $colores : [];
        $perfil_info = is_array($perfil_info) ? $perfil_info : [];

        // 5. Incluir la vista
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

