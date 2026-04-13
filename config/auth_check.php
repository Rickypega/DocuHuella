<?php
// Este archivo tiene que incluirse al principio de las vistas protegidas para expulsar intrusos.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

// 1. Si ni siquiera tiene sesión, pa' fuera
if (!isset($_SESSION['id_usuario'])) {
    // IMPORTANTE: db.php es incluido por index, pero si alguien llama a la vista directo, URL_BASE puede no existir
    if(!defined('URL_BASE')) { include_once __DIR__.'/db.php'; }
    header("Location: " . URL_BASE . "/login");
    exit();
}

// 2. Conectar a la BD para verificar si lo suspendieron en tiempo real
// Ajustamos la ruta asumiendo que auth_check.php está en la raíz de /controllers
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

try {
    $stmt = $db->prepare("SELECT Estado FROM usuarios WHERE ID_Usuario = :id_u");
    $stmt->bindParam(':id_u', $_SESSION['id_usuario']);
    $stmt->execute();
    $estado_actual = $stmt->fetchColumn();

    // 3. ¡EL EXPULSOR! Si el usuario está 'Suspendido' o 'Eliminado' (cualquier cosa distinta a 'Activo')
    if ($estado_actual !== 'Activo') {
        session_unset();
        session_destroy();
        header("Location: " . URL_BASE . "/login?error=cuenta_suspendida");
        exit();
    }
} catch (PDOException $e) {
    // Si hay error de BD, por seguridad lo sacamos pa' fuera
    header("Location: " . URL_BASE . "/login?error=error_sistema");
    exit();
}
?>
