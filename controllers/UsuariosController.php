<?php
session_start();

require_once '../config/db.php';
require_once '../models/Usuario.php';

class UsuariosController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $database = new Database();
            $db = $database->getConnection();
            $usuario = new Usuario($db);

            $correo_ingresado = $_POST['correo'];
            $password_ingresada = $_POST['contrasena'];

            $usuario->correo = $correo_ingresado;
            
            // Buscamos al usuario por correo
            $datos_usuario = $usuario->login(); 

            if ($datos_usuario) { 

                // 1. VERIFICAR BLOQUEO PREVIO (Fail-Fast)
                if (isset($datos_usuario['Estado']) && $datos_usuario['Estado'] !== 'Activo') {
                    header("Location: ../views/login.php?error=cuenta_suspendida");
                    exit();
                }

                // 2. VALIDAR CONTRASEÑA
                if (password_verify($password_ingresada, $datos_usuario['Contrasena'])) {
                    
                    // LOGIN EXITOSO: Limpiamos los intentos fallidos
                    $usuario->resetearIntentos($datos_usuario['ID_Usuario']);

                    // Datos básicos de la cuenta en sesión
                    $_SESSION['id_usuario'] = $datos_usuario['ID_Usuario'];
                    $_SESSION['id_rol']     = $datos_usuario['ID_Rol'];
                    $_SESSION['correo']     = $datos_usuario['Correo'];

                    // Cargar contexto (clínica e identidad)
                    $this->cargarContextoUsuario($db, $_SESSION['id_usuario'], $_SESSION['id_rol']);

                    // Redirección por rol
                    $this->redireccionarPorRol($_SESSION['id_rol']);
                    
                } else {
                    // CONTRASEÑA INCORRECTA: Lógica de bloqueo
                    $usuario->registrarFallo($datos_usuario['ID_Usuario']);
                    
                    // Calculamos los intentos (sumamos 1 al valor que ya traíamos de la DB)
                    $intentos_actuales = $datos_usuario['Intentos_Fallidos'] + 1;

                    if ($intentos_actuales >= 5) {
                        // BLOQUEO DEFINITIVO
                        $usuario->id_usuario = $datos_usuario['ID_Usuario'];
                        $usuario->estado = 'Suspendido';
                        $usuario->cambiarEstado();
                        
                        header("Location: ../views/login.php?error=cuenta_suspendida");
                    } elseif ($intentos_actuales >= 3) {
                        // ADVERTENCIA (Intentos 3 y 4)
                        $restantes = 5 - $intentos_actuales;
                        header("Location: ../views/login.php?error=advertencia&restantes=" . $restantes);
                    } else {
                        // ERROR NORMAL (Intentos 1 y 2)
                        header("Location: ../views/login.php?error=credenciales");
                    }
                    exit();
                }
            } else {
                // El correo ni siquiera existe
                header("Location: ../views/login.php?error=credenciales");
                exit();
            }
        }
    }

    /**
     * Esta función es el "GPS" del sistema. 
     */
    private function cargarContextoUsuario($db, $id_usuario, $rol) {
        switch ($rol) {
            case 1: // ADMINISTRADOR
                $query = "SELECT a.ID_Admin, c.ID_Clinica, a.Nombre 
                          FROM Administrador a 
                          LEFT JOIN Clinicas c ON a.ID_Admin = c.ID_Admin 
                          WHERE a.ID_Usuario = :id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($res) {
                    $_SESSION['id_perfil']  = $res['ID_Admin'];
                    $_SESSION['id_clinica'] = $res['ID_Clinica'];
                    $_SESSION['nombre']     = $res['Nombre'];
                }
                break;

            case 2: // VETERINARIO
                $query = "SELECT ID_Veterinario, ID_Clinica, Nombre FROM Veterinarios WHERE ID_Usuario = :id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($res) {
                    $_SESSION['id_perfil']  = $res['ID_Veterinario'];
                    $_SESSION['id_clinica'] = $res['ID_Clinica'];
                    $_SESSION['nombre']     = $res['Nombre'];
                }
                break;

            case 3: // CUIDADOR
                $query = "SELECT ID_Cuidador, Nombre FROM Cuidadores WHERE ID_Usuario = :id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($res) {
                    $_SESSION['id_perfil'] = $res['ID_Cuidador'];
                    $_SESSION['nombre']    = $res['Nombre'];
                }
                break;

            case 4: // SUPERADMIN
                $_SESSION['nombre'] = "Super Administrador";
                break;
        }
    }

    private function redireccionarPorRol($rol) {
        switch($rol) {
            case 4: header("Location: ../views/superadmin/dashboard.php"); break;
            case 1: header("Location: ../views/admin/dashboard.php"); break;
            case 2: header("Location: ../views/veterinario/dashboard.php"); break;
            case 3: header("Location: ../views/cuidador/dashboard.php"); break;
            default: header("Location: ../views/login.php"); break;
        }
        exit();
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/login.php");
        exit();
    }
}

// Ruteo
if (isset($_GET['action'])) {
    $controlador = new UsuariosController();
    if ($_GET['action'] == 'login') $controlador->login();
    else if ($_GET['action'] == 'logout') $controlador->logout();
}
?>