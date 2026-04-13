<?php

require_once 'models/Usuario.php';

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
                    header("Location: " . URL_BASE . "/login?error=cuenta_suspendida");
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
                    if ($datos_usuario['ID_Rol'] != 4 and $datos_usuario['ID_Rol'] != 3) {
                    $usuario->registrarFallo($datos_usuario['ID_Usuario']);
                    $intentos_actuales = $datos_usuario['Intentos_Fallidos'] + 1;

                    if ($intentos_actuales >= 5) {
                        $usuario->id_usuario = $datos_usuario['ID_Usuario'];
                        $usuario->estado = 'Suspendido';
                        $usuario->cambiarEstado();
                        
                        // Enviamos error de cuenta suspendida
                        header("Location: " . URL_BASE . "/login?error=cuenta_suspendida");
                    } elseif ($intentos_actuales >= 3) {
                        $restantes = 5 - $intentos_actuales;
                        // Enviamos el error de advertencia y pasamos cuántos quedan
                        header("Location: " . URL_BASE . "/login?error=advertencia&restantes=" . $restantes);
                    } else {
                        // El error de siempre para intentos fallidos
                        header("Location: " . URL_BASE . "/login?error=credenciales");
                    }
                    } else {
                        
                        header("Location: " . URL_BASE . "/login?error=credenciales");
                    }
                    exit();
                }
            } else {
                // El correo ni siquiera existe
                header("Location: " . URL_BASE . "/login?error=credenciales");
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
                          FROM administrador a 
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
                    $_SESSION['sexo']       = 'M'; // Default masculino para Admin
                }
                break;

            case 2: // VETERINARIO
                $query = "SELECT ID_Veterinario, ID_Clinica, Nombre, Sexo FROM veterinarios WHERE ID_Usuario = :id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($res) {
                    $_SESSION['id_perfil']  = $res['ID_Veterinario'];
                    $_SESSION['id_clinica'] = $res['ID_Clinica'];
                    $_SESSION['nombre']     = $res['Nombre'];
                    $_SESSION['sexo']       = $res['Sexo'] ?? 'M';
                }
                break;

            case 3: // CUIDADOR
                $query = "SELECT ID_Cuidador, Nombre, Sexo FROM cuidadores WHERE ID_Usuario = :id LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id_usuario);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($res) {
                    $_SESSION['id_perfil'] = $res['ID_Cuidador'];
                    $_SESSION['nombre']    = $res['Nombre'];
                    $_SESSION['sexo']      = $res['Sexo'] ?? 'M';
                }
                break;

            case 4: // SUPERADMIN
                $_SESSION['nombre'] = "Super Admin";
                $_SESSION['sexo'] = 'M';
                break;
        }
    }

    private function redireccionarPorRol($rol) {
        switch($rol) {
            case 4: $path = "/superadmin/dashboard"; break;
            case 1: $path = "/admin/dashboard"; break;
            case 2: $path = "/veterinario/dashboard"; break;
            case 3: $path = "/cuidador/dashboard"; break;
            default: $path = "/login"; break;
        }
        header("Location: ". URL_BASE . $path);
        exit();
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: " . URL_BASE . "/login");
        exit();
    }

    

// Muestra la Landing Page
public function index() {
    include_once 'views/home.php';
}

// Muestra el formulario de Login
public function showLogin() {
    include_once 'views/login.php';
}

// Muestra el formulario de Registro
public function showRegistro() {
    include_once 'views/registro.php';
}

public function privacidad() {
    include_once 'views/privacidad.php';
}

public function terminos() {
    include_once 'views/terminos.php';
}
}