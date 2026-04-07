<?php
session_start();
require_once '../config/db.php';
require_once '../models/Usuario.php';
require_once '../models/Cuidador.php'; 

class RegistroController {
    
   public function registrarCuidador() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // 1. Captura de datos
            $correo = $_POST['correo'];
            $confirmar_correo = $_POST['confirmar_correo'];
            $contrasena = $_POST['contrasena'];
            $confirmar_contrasena = $_POST['confirmar_contrasena'];

            // 2. Validaciones básicas de coincidencia
            if ($correo !== $confirmar_correo) {
                $this->regresarConError("correo_no_coincide", $_POST);
            }
            
            if ($contrasena !== $confirmar_contrasena) {
                $this->regresarConError("pass_no_coincide", $_POST);
            }

            // 2.1 Verificar mayoría de edad (18+ años)
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $fecha_nac_obj = new DateTime($fecha_nacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nac_obj)->y;

            if ($edad < 18) {
                $this->regresarConError("menor_de_edad", $_POST);
            }

            // Conexión
            $database = new Database();
            $db = $database->getConnection();

            // 3. Crear el Usuario (Credenciales)
            $usuario = new Usuario($db);
            $usuario->correo = $correo;
            $usuario->contrasena = $contrasena;
            $usuario->id_rol = 3; // Cuidador

            $resultado_usuario = $usuario->registrarUsuario();

            if ($resultado_usuario === true) {
                
                // 4. Obtener el ID recién creado 
                $id_usuario_nuevo = $db->lastInsertId(); 
                
                // Cédula: Quitamos todo lo que no sea número
                $cedula_limpia = preg_replace('/[^0-9]/', '', $_POST['cedula']);
                // Si tiene los 11 dígitos, la formateamos. Si no, la guardamos como llegó para no romper el string.
                $cedula_final = (strlen($cedula_limpia) == 11) 
                                ? substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1) 
                                : $_POST['cedula'];

                // Teléfono: Quitamos todo lo que no sea número
                $telefono_limpio = preg_replace('/[^0-9]/', '', $_POST['telefono']);
                // Si tiene 10 o más dígitos, lo formateamos.
                $telefono_final = (strlen($telefono_limpio) >= 10) 
                                  ? substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4) 
                                  : $_POST['telefono'];


                // 5. Crear el Perfil del Cuidador
                $cuidador = new Cuidador($db);
                $cuidador->id_usuario = $id_usuario_nuevo;
                $cuidador->nombre = $_POST['nombre'];
                $cuidador->apellido = $_POST['apellido'];
                
                // Usamos nuestras variables ya limpias y formateadas
                $cuidador->cedula = $cedula_final; 
                $cuidador->telefono = $telefono_final; 
                
                $cuidador->direccion = $_POST['direccion'];
                $cuidador->fecha_nacimiento = $_POST['fecha_nacimiento'];
                $cuidador->sexo = $_POST['sexo'];

                if ($cuidador->registrarse()) {
                    header("Location: ../views/login.php?exito=registrado");
                    exit();
                } else {
                    $this->regresarConError("perfil_fallo", $_POST);
                }

            } elseif ($resultado_usuario === 'correo_duplicado') {
                $this->regresarConError("correo_ya_existe", $_POST);
            } else {
                $this->regresarConError("error_desconocido", $_POST);
            }
        }
    }

    /**
     * Función auxiliar para no repetir código de redirección
     */
    private function regresarConError($error, $datos) {
        $_SESSION['datos_temporales'] = $datos;
        header("Location: ../views/registro.php?error=" . $error);
        exit();
    }
}

// Ruteo
if (isset($_GET['action']) && $_GET['action'] == 'registrar_cuidador') {
    $controlador = new RegistroController();
    $controlador->registrarCuidador();
}