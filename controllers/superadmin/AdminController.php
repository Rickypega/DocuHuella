<?php
session_start();
require_once '../../config/db.php';

class AdminController {

    public function registrarFranquicia() {
        // SEGURIDAD: Solo el SuperAdmin (Rol 4)
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
            header("Location: ../../views/login.php?error=acceso_denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $database = new Database();
            $db = $database->getConnection();

            if ($db === null) {
                header("Location: ../../views/superadmin/administrador.php?error=error_conexion");
                exit();
            }

            try {
                // 1. INICIAR TRANSACCIÓN
                $db->beginTransaction();

                // --- PASO A: CREAR EL USUARIO ---
                $query_user = "INSERT INTO usuarios (Correo, Contrasena, ID_Rol) VALUES (:correo, :pass, 1)";
                $stmt_user = $db->prepare($query_user);
                $pass_hash = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
                $stmt_user->bindParam(':correo', $_POST['correo']);
                $stmt_user->bindParam(':pass', $pass_hash);
                
                if (!$stmt_user->execute()) throw new Exception("No se pudo crear la cuenta de usuario.");
                $id_usuario_nuevo = $db->lastInsertId();

                // --- PASO B: CREAR EL ADMINISTRADOR (AHORA CON TELÉFONO) ---
                $query_admin = "INSERT INTO administrador (ID_Usuario, Nombre, Apellido, Cedula, Telefono) 
                                VALUES (:id_u, :nom, :ape, :ced, :tel)";
                $stmt_admin = $db->prepare($query_admin);
                $stmt_admin->bindParam(':id_u', $id_usuario_nuevo);
                $stmt_admin->bindParam(':nom', $_POST['nombre']);
                $stmt_admin->bindParam(':ape', $_POST['apellido']);
                $stmt_admin->bindParam(':ced', $_POST['cedula']);
                $stmt_admin->bindParam(':tel', $_POST['telefono']); // Atrapamos el nuevo campo
                
                if (!$stmt_admin->execute()) throw new Exception("No se pudo crear el perfil del administrador.");
                $id_admin_nuevo = $db->lastInsertId();

                // --- PASO C: CREAR LA CLÍNICA ---
                $query_clinica = "INSERT INTO clinicas (ID_Admin, Nombre_Sucursal, RNC, Direccion) 
                                  VALUES (:id_a, :nom_c, :rnc, :dir)";
                $stmt_clinica = $db->prepare($query_clinica);
                $stmt_clinica->bindParam(':id_a', $id_admin_nuevo);
                $stmt_clinica->bindParam(':nom_c', $_POST['nombre_clinica']);
                $stmt_clinica->bindParam(':rnc', $_POST['rnc']);
                $stmt_clinica->bindParam(':dir', $_POST['direccion_clinica']);

                if (!$stmt_clinica->execute()) throw new Exception("No se pudo registrar la clínica.");

                // 2. ÉXITO
                $db->commit();
                header("Location: ../../views/superadmin/administrador.php?exito=franquicia_creada");
                exit();

            } catch (Exception $e) {
                // 3. ERROR: Deshacer
                if ($db->inTransaction()) $db->rollBack();
                header("Location: ../../views/superadmin/administrador.php?error=fallo_registro&detalle=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header("Location: ../../views/superadmin/administrador.php");
            exit();
        }
    }
}

// Ruteo
if (isset($_GET['action']) && $_GET['action'] == 'registrar') {
    $controlador = new AdminController();
    $controlador->registrarFranquicia();
} else {
    header("Location: ../../views/superadmin/administrador.php");
    exit();
}