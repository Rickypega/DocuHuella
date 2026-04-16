<?php
// controllers/PerfilController.php

require_once 'config/db.php';

class PerfilController {

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            
            $id_usuario = $_SESSION['id_usuario'];
            $id_perfil  = $_SESSION['id_perfil'] ?? null;
            $id_rol     = $_SESSION['id_rol'];

            $telefono        = $_POST['telefono'] ?? null;
            $direccion       = $_POST['direccion'] ?? null;
            $password_actual = trim($_POST['password_actual']);
            $password_nueva  = !empty($_POST['password_nueva']) ? trim($_POST['password_nueva']) : null;

            try {
                // 1. Validar la contraseña actual de TODO usuario
                $stmt = $db->prepare("SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id");
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $hash = $stmt->fetchColumn();

                if (!$hash || !password_verify($password_actual, $hash)) {
                    // Contraseña incorrecta
                    header("Location: " . $_SERVER['HTTP_REFERER'] . (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . "status_perfil=error_pass");
                    exit();
                }

                // 2. Si hay nueva contraseña, actualizarla en Usuarios
                if ($password_nueva) {
                    $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                    $stmtPass = $db->prepare("UPDATE usuarios SET Contrasena = :pass WHERE ID_Usuario = :id");
                    $stmtPass->bindParam(':pass', $nuevo_hash);
                    $stmtPass->bindParam(':id', $id_usuario);
                    $stmtPass->execute();
                }

                // 3. Actualizar datos específicos según Rol (solo si proveen $telefono y existen en Perfil)
                if ($telefono) {
                    if ($id_rol == 1 && $id_perfil) {
                        // Administrador
                        $stmtAdmin = $db->prepare("UPDATE administrador SET Telefono = :tel WHERE ID_Admin = :id_perf");
                        $stmtAdmin->bindParam(':tel', $telefono);
                        $stmtAdmin->bindParam(':id_perf', $id_perfil);
                        $stmtAdmin->execute();
                    }
                    elseif ($id_rol == 2 && $id_perfil) {
                        // Veterinario
                        $stmtVet = $db->prepare("UPDATE veterinarios SET Telefono = :tel, Direccion = :dir WHERE ID_Veterinario = :id_perf");
                        $stmtVet->bindParam(':tel', $telefono);
                        $stmtVet->bindParam(':dir', $direccion);
                        $stmtVet->bindParam(':id_perf', $id_perfil);
                        $stmtVet->execute();
                    }
                    elseif ($id_rol == 3 && $id_perfil) {
                        // Cuidador
                        $stmtCuid = $db->prepare("UPDATE cuidadores SET Telefono = :tel, Direccion = :dir WHERE ID_Cuidador = :id_perf");
                        $stmtCuid->bindParam(':tel', $telefono);
                        $stmtCuid->bindParam(':dir', $direccion);
                        $stmtCuid->bindParam(':id_perf', $id_perfil);
                        $stmtCuid->execute();
                    }
                }

                header("Location: " . $_SERVER['HTTP_REFERER'] . (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . "status_perfil=success");
                exit();

            } catch (PDOException $e) {
                header("Location: " . $_SERVER['HTTP_REFERER'] . (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . "status_perfil=error_db");
                exit();
            }
        }
    }
}
?>

