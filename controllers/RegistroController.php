<?php
session_start();
require_once '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Cuidador.php'; // Necesitaremos el modelo del Cuidador

class RegistroController {
    
    public function registrarCuidador() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // 1. Capturamos los datos de confirmación
            $correo = $_POST['correo'];
            $confirmar_correo = $_POST['confirmar_correo'];
            $contrasena = $_POST['contrasena'];
            $confirmar_contrasena = $_POST['confirmar_contrasena'];

            // 2. Validaciones de seguridad (Lo que planeaste con tu equipo)
            if ($correo !== $confirmar_correo) {
                header("Location: ../views/registro.php?error=correo_no_coincide");
                exit();
            }
            
            if ($contrasena !== $confirmar_contrasena) {
                header("Location: ../views/registro.php?error=pass_no_coincide");
                exit();
            }

            // Conexión a la base de datos
            $database = new Database();
            $db = $database->getConnection();

            // 3. Crear primero el Usuario (Credenciales de acceso)
            $usuario = new Usuario($db);
            $usuario->correo = $correo;
            $usuario->contrasena = $contrasena;
            $usuario->id_rol = 3; // Le asignamos el Rol 3 (Cuidador)

            if ($usuario->registrarUsuario()) {
                
                // 4. Buscar el ID del usuario que acabamos de crear
                // Reutilizamos tu método login() del modelo para encontrarlo por su correo
                $usuario->correo = $correo;
                $stmt = $usuario->login(); 
                
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id_usuario_nuevo = $row['ID_Usuario']; // ¡Aquí tenemos el ID!

                    // 5. Crear el Perfil del Cuidador enlazado al Usuario
                    $cuidador = new Cuidador($db);
                    $cuidador->id_usuario = $id_usuario_nuevo;
                    $cuidador->nombre = $_POST['nombre'];
                    $cuidador->apellido = $_POST['apellido'];
                    $cuidador->cedula = $_POST['cedula'];
                    $cuidador->telefono = $_POST['telefono'];
                    $cuidador->direccion = $_POST['direccion'];
                    $cuidador->fecha_nacimiento = $_POST['fecha_nacimiento'];
                    $cuidador->sexo = $_POST['sexo'];

                    // Ejecutamos el registro del perfil
                    if ($cuidador->registrarse()) {
                        // ¡Éxito total! Lo mandamos al login con un mensaje de triunfo
                        header("Location: ../views/login.php?exito=registrado");
                        exit();
                    } else {
                        header("Location: ../views/registro.php?error=perfil_fallo");
                        exit();
                    }
                }
            } else {
                // Si falla al crear el usuario (ej: el correo ya está registrado)
                header("Location: ../views/registro.php?error=usuario_existe");
                exit();
            }
        }
    }
}

// ========================================================================
// LÓGICA DE RUTEO SIMPLE
// ========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'registrar_cuidador') {
    $controlador = new RegistroController();
    $controlador->registrarCuidador();
}
?>