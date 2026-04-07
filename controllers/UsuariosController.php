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
            
            
            $datos_usuario = $usuario->login(); 

            if ($datos_usuario) { // Si encontró el correo (no devolvió false)

                if (password_verify($password_ingresada, $datos_usuario['Contrasena'])) {
                    
                    // Verificamos si la columna Estado es distinta de 'Activo'
                    if (isset($datos_usuario['Estado']) && $datos_usuario['Estado'] !== 'Activo') {
                        header("Location: ../views/login.php?error=cuenta_suspendida");
                        exit();
                    }
                    
                    // Si pasó el filtro, le damos acceso normal...

                    // 1. Datos básicos de la cuenta
                    $_SESSION['id_usuario'] = $datos_usuario['ID_Usuario'];
                    $_SESSION['id_rol']     = $datos_usuario['ID_Rol'];
                    $_SESSION['correo']     = $datos_usuario['Correo'];

                    // 2. BUSCAR IDENTIDAD Y CLÍNICA 
                    // Dependiendo del rol, necesitamos saber su ID de tabla y su Clínica
                    $this->cargarContextoUsuario($db, $_SESSION['id_usuario'], $_SESSION['id_rol']);

                    // 3. Redirección inteligente
                    $this->redireccionarPorRol($_SESSION['id_rol']);
                    
                } else {
                    header("Location: ../views/login.php?error=credenciales");
                    exit();
                }
            } else {
                header("Location: ../views/login.php?error=credenciales");
                exit();
            }
        }
    }

    /**
     * Esta función es el "GPS" del sistema. 
     * Ubica al usuario en su clínica y perfil correspondiente.
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
                    // El cuidador no tiene ID_Clinica fijo, él puede ir a varias.
                }
                break;

            case 4: // SUPERADMIN
                $_SESSION['nombre'] = "Super Administrador";
                // El SuperAdmin no tiene clínica porque él es el dueño de TODO el software.
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