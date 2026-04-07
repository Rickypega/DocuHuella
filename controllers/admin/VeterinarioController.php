<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Indicamos al navegador que la respuesta será un JSON
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../models/Usuario.php';
require_once '../../models/Veterinario.php';

class VeterinarioController {
    
    public function registrar() {
        // 1. SEGURIDAD: Solo Administradores (Rol 1)
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            echo json_encode([
                'status' => 'error', 
                'type' => 'acceso_denegado', 
                'message' => 'No tienes permisos para realizar esta acción.'
            ]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $database = new Database();
            $db = $database->getConnection();

            // 2. VERIFICACIÓN DE IDENTIDAD DEL ADMINISTRADOR (Firma Digital)
            $pass_admin_ingresada = $_POST['contrasena_admin'] ?? '';
            $id_usuario_admin = $_SESSION['id_usuario'];

            $query_check = "SELECT Contrasena FROM Usuarios WHERE ID_Usuario = :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':id', $id_usuario_admin);
            $stmt_check->execute();
            $admin_db = $stmt_check->fetch(PDO::FETCH_ASSOC);

            // Validar clave del admin
            if (!$admin_db || !password_verify($pass_admin_ingresada, $admin_db['Contrasena'])) {
                echo json_encode([
                    'status' => 'error', 
                    'type' => 'auth_admin_fallida'
                ]);
                exit();
            }

            // 3. CAPTURA Y LIMPIEZA DE DATOS
            $correo = $_POST['correo'] ?? '';
            $confirmar_correo = $_POST['confirmar_correo'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

            // Validaciones de coincidencia
            if ($correo !== $confirmar_correo || $contrasena !== $confirmar_contrasena) {
                echo json_encode([
                    'status' => 'error', 
                    'type' => 'datos_no_coinciden'
                ]);
                exit();
            }

            // Limpieza de Cédula y Teléfono (Guarda el formato 000-0000000-0)
            $cedula_limpia = preg_replace('/[^0-9]/', '', $_POST['cedula']);
            $cedula_final = substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1);

            $telefono_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
            $telefono_final = substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4);

            // 4. CREAR EL USUARIO (Credenciales)
            $usuario = new Usuario($db);
            $usuario->correo = $correo;
            $usuario->contrasena = $contrasena; 
            $usuario->id_rol = 2; // Rol fijo: Veterinario

            $resultado_usuario = $usuario->registrarUsuario();

            if ($resultado_usuario === true) {
                $id_usuario_nuevo = $db->lastInsertId();

                // 5. CREAR EL PERFIL DEL VETERINARIO
                $vet = new Veterinario($db);
                $vet->id_usuario = $id_usuario_nuevo;
                $vet->id_clinica = $_POST['id_clinica'];
                $vet->nombre = $_POST['nombre'];
                $vet->apellido = $_POST['apellido'];
                $vet->cedula = $cedula_final;
                $vet->telefono = $telefono_final;
                $vet->sexo = $_POST['sexo'];
                $vet->fecha_nacimiento = $_POST['fecha_nacimiento'];
                $vet->especialidad = $_POST['especialidad'];
                $vet->exequatur = $_POST['exequatur'];
                $vet->colegiatura = $_POST['colegiatura'];
                $vet->direccion = "N/A"; 

                if ($vet->registrarPerfil()) {
                    // ÉXITO TOTAL
                    echo json_encode(['status' => 'success']);
                    exit();
                } else {
                    echo json_encode([
                        'status' => 'error', 
                        'type' => 'error_perfil'
                    ]);
                    exit();
                }

            } elseif ($resultado_usuario === 'correo_duplicado') {
                echo json_encode([
                    'status' => 'error', 
                    'type' => 'correo_ya_existe'
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'type' => 'error_desconocido'
                ]);
                exit();
            }
        }
    }
}

// Ruteo automático
if (isset($_GET['action']) && $_GET['action'] == 'registrar') {
    $controlador = new VeterinarioController();
    $controlador->registrar();
}
?>