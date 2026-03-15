<?php
// Iniciamos la sesión para poder guardar los datos del usuario logueado
session_start();

// Requerimos la conexión a la base de datos y el modelo
require_once '../config/db.php';
require_once '../models/Usuario.php';

class UsuariosController {
    
    // Método para procesar el inicio de sesión
    public function login() {
        // Verificamos que los datos vengan por el método POST (del formulario)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $database = new Database();
            $db = $database->getConnection();
            $usuario = new Usuario($db);

            // Capturamos lo que el usuario escribió en el login
            $correo_ingresado = $_POST['correo'];
            $password_ingresada = $_POST['contrasena'];

            // Asignamos el correo al modelo y ejecutamos tu método login()
            $usuario->correo = $correo_ingresado;
            $stmt = $usuario->login(); 

            // Verificamos si la consulta encontró al menos 1 fila (el correo existe)
            if ($stmt->rowCount() > 0) {
                // Extraemos los datos de ese usuario en un arreglo asociativo
                $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificamos si la contraseña coincide con el hash guardado
                if (password_verify($password_ingresada, $datos_usuario['Contrasena'])) {
                    
                    // ¡Login exitoso! Guardamos los datos en variables de sesión
                    $_SESSION['id_usuario'] = $datos_usuario['ID_Usuario'];
                    $_SESSION['id_rol'] = $datos_usuario['ID_Rol'];
                    $_SESSION['correo'] = $datos_usuario['Correo'];

                    // Redirigimos dependiendo del rol (1=Admin, 2=Vet, 3=Cuidador)
                    if ($_SESSION['id_rol'] == 1) {
                        header("Location: ../views/admin/dashboard.php");
                    } else if ($_SESSION['id_rol'] == 2) {
                        header("Location: ../views/veterinario/dashboard.php");
                    } else {
                        header("Location: ../views/cuidador/dashboard.php");
                    }
                    exit();
                } else {
                    // Contraseña incorrecta
                    header("Location: ../views/login.php?error=credenciales");
                    exit();
                }
            } else {
                // El correo no existe
                header("Location: ../views/login.php?error=credenciales");
                exit();
            }
        }
    }

    // Método para cerrar sesión
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/login.php");
        exit();
    }
}

// ========================================================================
// LÓGICA DE RUTEO SIMPLE
// Esto detecta qué acción queremos ejecutar desde la URL o el formulario
// ========================================================================
if (isset($_GET['action'])) {
    $controlador = new UsuariosController();
    
    if ($_GET['action'] == 'login') {
        $controlador->login();
    } else if ($_GET['action'] == 'logout') {
        $controlador->logout();
    }
}
?>