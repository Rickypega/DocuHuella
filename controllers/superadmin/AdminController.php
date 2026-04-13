<?php
session_start();
require_once '../../config/db.php';

class AdminController {

    // Método de seguridad global
    private function verificarSeguridad() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
            header("Location: ../../views/login.php?error=acceso_denegado");
            exit();
        }
    }

    // Método para validar la Contraseña del SuperAdmin
    private function validarMasterKey() {
        if (!isset($_POST['admin_auth']) || empty($_POST['admin_auth'])) {
            header("Location: ../../views/superadmin/administrador.php?error=clave_requerida");
            exit();
        }
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT Contrasena FROM Usuarios WHERE ID_Usuario = :id");
        $stmt->bindParam(':id', $_SESSION['id_usuario']);
        $stmt->execute();
        $hash = $stmt->fetchColumn();
        
        if (!password_verify($_POST['admin_auth'], $hash)) {
            header("Location: ../../views/superadmin/administrador.php?error=clave_incorrecta");
            exit();
        }
    }

    // 1. CREAR
    public function registrarFranquicia() {
        $this->verificarSeguridad();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validarMasterKey();
            
            // Cédula
            $cedula_limpia = preg_replace('/[^0-9]/', '', $_POST['cedula']);
            $cedula_final = (strlen($cedula_limpia) == 11) 
                            ? substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1) 
                            : $_POST['cedula'];

            // Teléfono
            $telefono_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
            $telefono_final = (strlen($telefono_limpio) >= 10) 
                              ? substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4) 
                              : $_POST['telefono'];
           
            // RNC (Solo números)
            $rnc_limpio = preg_replace('/[^0-9]/', '', $_POST['rnc']);

            $db = (new Database())->getConnection();

            // 🛡️ --- VALIDACIÓN PREVIA DE DUPLICADOS --- 🛡️
            // 1. Revisar si el Correo ya existe
            $stmt = $db->prepare("SELECT ID_Usuario FROM usuarios WHERE Correo = :correo");
            $stmt->execute([':correo' => $_POST['correo']]);
            if ($stmt->rowCount() > 0) {
                header("Location: ../../views/superadmin/administrador.php?error=correo_duplicado");
                exit();
            }

            // 2. Revisar si la Cédula ya existe
            $stmt = $db->prepare("SELECT ID_Admin FROM administrador WHERE Cedula = :cedula");
            $stmt->execute([':cedula' => $cedula_final]);
            if ($stmt->rowCount() > 0) {
                header("Location: ../../views/superadmin/administrador.php?error=cedula_duplicada");
                exit();
            }

            // 3. Revisar si el RNC ya existe
            $stmt = $db->prepare("SELECT ID_Clinica FROM clinicas WHERE RNC = :rnc");
            $stmt->execute([':rnc' => $rnc_limpio]);
            if ($stmt->rowCount() > 0) {
                header("Location: ../../views/superadmin/administrador.php?error=rnc_duplicado");
                exit();
            }
            // 🛡️ --- FIN VALIDACIÓN PREVIA --- 🛡️

            try {
                $db->beginTransaction();

                // Crear Usuario
                $query_user = "INSERT INTO usuarios (Correo, Contrasena, ID_Rol, Estado) VALUES (:correo, :pass, 1, 'Activo')";
                $stmt_user = $db->prepare($query_user);
                $pass_hash = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
                $stmt_user->bindParam(':correo', $_POST['correo']);
                $stmt_user->bindParam(':pass', $pass_hash);
                $stmt_user->execute();
                $id_usuario_nuevo = $db->lastInsertId();

                // Crear Admin (Usando variables limpias)
                $query_admin = "INSERT INTO administrador (ID_Usuario, Nombre, Apellido, Cedula, Telefono) 
                                VALUES (:id_u, :nom, :ape, :ced, :tel)";
                $stmt_admin = $db->prepare($query_admin);
                $stmt_admin->bindParam(':id_u', $id_usuario_nuevo);
                $stmt_admin->bindParam(':nom', $_POST['nombre']);
                $stmt_admin->bindParam(':ape', $_POST['apellido']);
                $stmt_admin->bindParam(':ced', $cedula_final); 
                $stmt_admin->bindParam(':tel', $telefono_final); 
                $stmt_admin->execute();
                $id_admin_nuevo = $db->lastInsertId();

                // Crear Clínica
                $query_clinica = "INSERT INTO clinicas (ID_Admin, Nombre_Sucursal, RNC, Direccion) 
                                  VALUES (:id_a, :nom_c, :rnc, :dir)";
                $stmt_clinica = $db->prepare($query_clinica);
                $stmt_clinica->bindParam(':id_a', $id_admin_nuevo);
                $stmt_clinica->bindParam(':nom_c', $_POST['nombre_clinica']);
                $stmt_clinica->bindParam(':rnc', $rnc_limpio); 
                $stmt_clinica->bindParam(':dir', $_POST['direccion_clinica']);
                $stmt_clinica->execute();

                $db->commit();
                header("Location: ../../views/superadmin/administrador.php?exito=franquicia_creada");
                exit();
            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                // Si ocurre otro tipo de error, lo mandamos como fallo genérico
                header("Location: ../../views/superadmin/administrador.php?error=fallo_registro");
                exit();
            }
        }
    }

    // 2. EDITAR 
    public function editarFranquicia() {
        $this->verificarSeguridad();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validarMasterKey();
            
            // 
            $telefono_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
            $telefono_final = (strlen($telefono_limpio) >= 10) 
                              ? substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4) 
                              : $_POST['telefono'];

            $db = (new Database())->getConnection();
            try {
                $db->beginTransaction();

                // Update Admin 
                $query_admin = "UPDATE administrador SET Nombre = :nom, Apellido = :ape, Telefono = :tel WHERE ID_Admin = :id_a";
                $stmt_admin = $db->prepare($query_admin);
                $stmt_admin->bindParam(':nom', $_POST['nombre']);
                $stmt_admin->bindParam(':ape', $_POST['apellido']);
                $stmt_admin->bindParam(':tel', $telefono_final); 
                $stmt_admin->bindParam(':id_a', $_POST['id_admin']);
                $stmt_admin->execute();

                // Update Clínica
                $query_clinica = "UPDATE clinicas SET Nombre_Sucursal = :nom_c WHERE ID_Admin = :id_a";
                $stmt_clinica = $db->prepare($query_clinica);
                $stmt_clinica->bindParam(':nom_c', $_POST['nombre_clinica']);
                $stmt_clinica->bindParam(':id_a', $_POST['id_admin']);
                $stmt_clinica->execute();

                $db->commit();
                header("Location: ../../views/superadmin/administrador.php?exito=franquicia_actualizada");
                exit();
            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                header("Location: ../../views/superadmin/administrador.php?error=fallo_actualizacion");
                exit();
            }
        }
    }

    // 3. SUSPENDER / REACTIVAR
    public function suspenderFranquicia() {
        $this->verificarSeguridad();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validarMasterKey();
            $db = (new Database())->getConnection();
            try {
                
                $query = "UPDATE Usuarios 
                        SET Estado = CASE 
                            WHEN Estado = 'Activo' THEN 'Inactivo' 
                            ELSE 'Activo' 
                        END,
                        Intentos_Fallidos = CASE 
                            WHEN Estado != 'Activo' THEN 0 
                            ELSE Intentos_Fallidos 
                        END
                        WHERE ID_Usuario = :id_u";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_u', $_POST['id_usuario']);
                $stmt->execute();

                header("Location: ../../views/superadmin/administrador.php?exito=estado_cambiado");
                exit();
            } catch (Exception $e) {
                header("Location: ../../views/superadmin/administrador.php?error=fallo_estado");
                exit();
            }
        }
    }
    // 4. ELIMINAR (Destrucción total)
    public function eliminarFranquicia() {
        $this->verificarSeguridad();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validarMasterKey();
            $db = (new Database())->getConnection();
            try {
                $db->beginTransaction();

                $stmt1 = $db->prepare("DELETE FROM clinicas WHERE ID_Admin = :id_a");
                $stmt1->bindParam(':id_a', $_POST['id_admin']);
                $stmt1->execute();

                $stmt2 = $db->prepare("DELETE FROM administrador WHERE ID_Admin = :id_a");
                $stmt2->bindParam(':id_a', $_POST['id_admin']);
                $stmt2->execute();

                $stmt3 = $db->prepare("DELETE FROM usuarios WHERE ID_Usuario = :id_u");
                $stmt3->bindParam(':id_u', $_POST['id_usuario']);
                $stmt3->execute();

                $db->commit();
                header("Location: ../../views/superadmin/administrador.php?exito=franquicia_eliminada");
                exit();
            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                header("Location: ../../views/superadmin/administrador.php?error=fallo_eliminacion");
                exit();
            }
        }
    }
}

// LÓGICA DE ENRUTAMIENTO (Switch central)
if (isset($_GET['action'])) {
    $controlador = new AdminController();
    
    switch ($_GET['action']) {
        case 'registrar':
            $controlador->registrarFranquicia();
            break;
        case 'editar':
            $controlador->editarFranquicia();
            break;
        case 'suspender':
            $controlador->suspenderFranquicia();
            break;
        case 'eliminar':
            $controlador->eliminarFranquicia();
            break;
        default:
            header("Location: ../../views/superadmin/administrador.php");
            exit();
    }
} else {
    header("Location: ../../views/superadmin/administrador.php");
    exit();
}