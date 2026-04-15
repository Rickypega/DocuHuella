<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Respuesta en formato JSON
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../models/Usuario.php';
require_once '../../models/Veterinario.php';

class VeterinarioController {
    
    public function registrar() {
        // 1. SEGURIDAD: Solo Administradores
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            echo json_encode(['status' => 'error', 'type' => 'acceso_denegado']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $database = new Database();
            $db = $database->getConnection();

            // 2. VALIDACIÓN INICIAL DE CAMPOS
            $campos_obligatorios = [
                'correo', 'confirmar_correo', 'contrasena', 'confirmar_contrasena',
                'nombre', 'apellido', 'cedula', 'telefono', 'sexo', 'fecha_nacimiento',
                'id_clinica', 'id_especialidad', 'exequatur', 'colegiatura', 'direccion',
                'contrasena_admin'
            ];

            foreach ($campos_obligatorios as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    echo json_encode(['status' => 'error', 'type' => 'campos_incompletos']);
                    exit();
                }
            }

            // 3. VERIFICACIÓN DE IDENTIDAD DEL ADMINISTRADOR
            $id_usuario_admin = $_SESSION['id_usuario'];
            $query_check = "SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':id', $id_usuario_admin);
            $stmt_check->execute();
            $admin_db = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$admin_db || !password_verify($_POST['contrasena_admin'], $admin_db['Contrasena'])) {
                echo json_encode(['status' => 'error', 'type' => 'auth_admin_fallida']);
                exit();
            }

            // 4. FORMATEO DE DATOS
            if ($_POST['correo'] !== $_POST['confirmar_correo'] || $_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
                echo json_encode(['status' => 'error', 'type' => 'datos_no_coinciden']);
                exit();
            }

            $cedula_limpia = preg_replace('/[^0-9]/', '', $_POST['cedula']);
            $cedula_final = substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1);

            $telefono_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
            $telefono_final = substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4);

            // =======================================================
            // 5. Registro
            // =======================================================
            $db->beginTransaction();

            try {
                // A. Intentamos crear el Usuario
                $usuario = new Usuario($db);
                $usuario->correo = $_POST['correo'];
                $usuario->contrasena = $_POST['contrasena'];
                $usuario->id_rol = 2; // Rol: Veterinario

                $resultado_usuario = $usuario->registrarUsuario();

                if ($resultado_usuario === 'correo_duplicado') {
                    throw new Exception('correo_ya_existe');
                }

                if (!$resultado_usuario) {
                    throw new Exception('error_usuario');
                }

                $id_usuario_nuevo = $db->lastInsertId();

                // B. Intentamos crear el Perfil de Veterinario
                $vet = new Veterinario($db);
                $vet->id_usuario = $id_usuario_nuevo;
                $vet->id_clinica = (int)$_POST['id_clinica'];
                $vet->nombre = $_POST['nombre'];
                $vet->apellido = $_POST['apellido'];
                $vet->cedula = $cedula_final;
                $vet->telefono = $telefono_final;
                $vet->sexo = $_POST['sexo'];
                $vet->fecha_nacimiento = $_POST['fecha_nacimiento'];
                $vet->id_especialidad = (int)$_POST['id_especialidad'];
                $vet->exequatur = $_POST['exequatur'];
                $vet->colegiatura = $_POST['colegiatura'];
                $vet->direccion = $_POST['direccion'];

                if (!$vet->registrarPerfil()) {
                    throw new Exception('error_perfil');
                }

                // 6. CIERRE EXITOSO: Guardamos todos los cambios de golpe
                $db->commit();
                echo json_encode(['status' => 'success']);

            } catch (Exception $e) {
                // 7. GESTIÓN DE FALLOS: Si algo salió mal, MySQL deshace todo (Rollback)
                $db->rollBack();
                
                $mensaje = $e->getMessage();
                $type = 'error_desconocido';

                // Detectar duplicados de MySQL (Código 23000)
                if (strpos($mensaje, '23000') !== false) {
                    if (strpos($mensaje, 'Cedula') !== false) {
                        $type = 'cedula_duplicada';
                    } elseif (strpos($mensaje, 'Exequatur') !== false) {
                        $type = 'exequatur_duplicado';
                    } elseif (strpos($mensaje, 'Colegiatura') !== false) {
                        $type = 'colegiatura_duplicada';
                    } elseif (strpos($mensaje, 'correo_ya_existe') !== false) {
                        $type = 'correo_ya_existe';
                    }
                } else {
                    $type = $mensaje; // Si es un error manual nuestro (ej: 'error_perfil')
                }
                
                echo json_encode([
                    'status' => 'error',
                    'type' => $type
                ]);
            }
        }
    }
    public function actualizar() {
        // 1. SEGURIDAD: Solo Administradores
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            echo json_encode(['status' => 'error', 'type' => 'acceso_denegado']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $database = new Database();
            $db = $database->getConnection();

            // 2. VALIDAR CAMPOS OBLIGATORIOS
            $campos = ['id_vet', 'id_usuario', 'telefono', 'id_especialidad', 'id_clinica', 'estado', 'direccion', 'contrasena_admin'];
            foreach ($campos as $campo) {
                if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
                    echo json_encode(['status' => 'error', 'type' => 'campos_incompletos']);
                    exit();
                }
            }

            // 3. VERIFICAR CONTRASEÑA DEL ADMINISTRADOR
            $id_usuario_admin = $_SESSION['id_usuario'];
            $stmt_check = $db->prepare("SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id");
            $stmt_check->bindParam(':id', $id_usuario_admin);
            $stmt_check->execute();
            $admin_db = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$admin_db || !password_verify($_POST['contrasena_admin'], $admin_db['Contrasena'])) {
                echo json_encode(['status' => 'error', 'type' => 'auth_admin_fallida']);
                exit();
            }

            // 4. FORMATEAR TELÉFONO
            $tel_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
            $tel_final  = substr($tel_limpio, 0, 3) . '-' . substr($tel_limpio, 3, 3) . '-' . substr($tel_limpio, 6, 4);

            $id_vet        = (int) $_POST['id_vet'];
            $id_usuario    = (int) $_POST['id_usuario'];
            $id_esp        = (int) $_POST['id_especialidad'];
            $id_clinica    = (int) $_POST['id_clinica'];
            $estado        = $_POST['estado'];
            $direccion     = trim($_POST['direccion']);

            // 5. ACTUALIZAR EN TRANSACCIÓN
            $db->beginTransaction();
            try {
                // Actualizar perfil del veterinario
                $stmt_vet = $db->prepare(
                    "UPDATE veterinarios
                     SET Telefono = :tel, ID_Especialidad = :esp, ID_Clinica = :cli, Direccion = :dir
                     WHERE ID_Veterinario = :id_vet"
                );
                $stmt_vet->bindParam(':tel',    $tel_final);
                $stmt_vet->bindParam(':esp',    $id_esp,     PDO::PARAM_INT);
                $stmt_vet->bindParam(':cli',    $id_clinica, PDO::PARAM_INT);
                $stmt_vet->bindParam(':dir',    $direccion);
                $stmt_vet->bindParam(':id_vet', $id_vet,     PDO::PARAM_INT);
                $stmt_vet->execute();

                // Actualizar estado en la tabla usuarios
                $stmt_usu = $db->prepare("UPDATE usuarios SET Estado = :estado WHERE ID_Usuario = :id_usu");
                $stmt_usu->bindParam(':estado',  $estado);
                $stmt_usu->bindParam(':id_usu',  $id_usuario, PDO::PARAM_INT);
                $stmt_usu->execute();

                $db->commit();
                echo json_encode(['status' => 'success']);

            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'type' => 'error_db', 'msg' => $e->getMessage()]);
            }
        }
    }
}

// Ejecución del ruteo
if (isset($_GET['action'])) {
    $controlador = new VeterinarioController();
    if ($_GET['action'] == 'registrar') {
        $controlador->registrar();
    } elseif ($_GET['action'] == 'actualizar') {
        $controlador->actualizar();
    }
}
