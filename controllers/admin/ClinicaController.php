<?php
session_start();
require_once '../../config/db.php';
require_once '../../models/Clinica.php';

class ClinicaController {

    // =============================================
    // REGISTRAR nueva sucursal
    // =============================================
    public function registrar() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            echo json_encode(['status' => 'error', 'type' => 'acceso_denegado']); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }

        $database = new Database();
        $db = $database->getConnection();

        // Validar campos obligatorios
        $campos = ['nombre_sucursal', 'direccion', 'telefono', 'rnc', 'contrasena_admin'];
        foreach ($campos as $c) {
            if (!isset($_POST[$c]) || trim($_POST[$c]) === '') {
                echo json_encode(['status' => 'error', 'type' => 'campos_incompletos']); exit();
            }
        }

        // Verificar contraseña del admin
        $id_usuario_admin = $_SESSION['id_usuario'];
        $stmt = $db->prepare("SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id");
        $stmt->bindParam(':id', $id_usuario_admin);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($_POST['contrasena_admin'], $admin['Contrasena'])) {
            echo json_encode(['status' => 'error', 'type' => 'auth_admin_fallida']); exit();
        }

        // Verificar RNC duplicado (dentro de las clínicas del mismo admin)
        $stmt_rnc = $db->prepare("SELECT ID_Clinica FROM clinicas WHERE RNC = :rnc AND ID_Admin = :id_admin");
        $stmt_rnc->bindParam(':rnc', $_POST['rnc']);
        $stmt_rnc->bindParam(':id_admin', $_SESSION['id_perfil']);
        $stmt_rnc->execute();
        if ($stmt_rnc->fetch()) {
            echo json_encode(['status' => 'error', 'type' => 'rnc_duplicado']); exit();
        }

        // Formatear teléfono
        $tel_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
        $tel_final  = substr($tel_limpio, 0, 3) . '-' . substr($tel_limpio, 3, 3) . '-' . substr($tel_limpio, 6, 4);

        $clinica = new Clinica($db);
        $clinica->id_admin        = $_SESSION['id_perfil'];
        $clinica->nombre_sucursal = trim($_POST['nombre_sucursal']);
        $clinica->direccion       = trim($_POST['direccion']);
        $clinica->telefono        = $tel_final;
        $clinica->rnc             = trim($_POST['rnc']);

        if ($clinica->registrarClinica()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'type' => 'error_db']);
        }
    }

    // =============================================
    // ACTUALIZAR datos de una sucursal
    // =============================================
    public function actualizar() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            echo json_encode(['status' => 'error', 'type' => 'acceso_denegado']); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }

        $database = new Database();
        $db = $database->getConnection();

        $campos = ['id_clinica', 'nombre_sucursal', 'direccion', 'telefono', 'rnc', 'estado', 'contrasena_admin'];
        foreach ($campos as $c) {
            if (!isset($_POST[$c]) || trim($_POST[$c]) === '') {
                echo json_encode(['status' => 'error', 'type' => 'campos_incompletos']); exit();
            }
        }

        // Verificar contraseña del admin
        $id_usuario_admin = $_SESSION['id_usuario'];
        $stmt = $db->prepare("SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id");
        $stmt->bindParam(':id', $id_usuario_admin);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($_POST['contrasena_admin'], $admin['Contrasena'])) {
            echo json_encode(['status' => 'error', 'type' => 'auth_admin_fallida']); exit();
        }

        // Verificar que la clínica pertenece a este admin
        $stmt_own = $db->prepare("SELECT ID_Clinica FROM clinicas WHERE ID_Clinica = :id AND ID_Admin = :id_admin");
        $stmt_own->bindParam(':id',       $_POST['id_clinica']);
        $stmt_own->bindParam(':id_admin', $_SESSION['id_perfil']);
        $stmt_own->execute();
        if (!$stmt_own->fetch()) {
            echo json_encode(['status' => 'error', 'type' => 'acceso_denegado']); exit();
        }

        // Formatear teléfono
        $tel_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
        $tel_final  = substr($tel_limpio, 0, 3) . '-' . substr($tel_limpio, 3, 3) . '-' . substr($tel_limpio, 6, 4);

        $clinica = new Clinica($db);
        $clinica->id_clinica      = (int) $_POST['id_clinica'];
        $clinica->nombre_sucursal = trim($_POST['nombre_sucursal']);
        $clinica->direccion       = trim($_POST['direccion']);
        $clinica->telefono        = $tel_final;
        $clinica->rnc             = trim($_POST['rnc']);

        // Actualizar datos principales
        $ok = $clinica->actualizarDatos();

        // Actualizar estado por separado
        $clinica->estado = $_POST['estado'];
        $clinica->cambiarEstado();

        if ($ok) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'type' => 'error_db']);
        }
    }
}

// Ruteo
if (isset($_GET['action'])) {
    $ctrl = new ClinicaController();
    if ($_GET['action'] === 'registrar') {
        $ctrl->registrar();
    } elseif ($_GET['action'] === 'actualizar') {
        $ctrl->actualizar();
    }
}
