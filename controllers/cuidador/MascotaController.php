<?php
require_once APP_PATH . '/models/Mascota.php';

class MascotaController
{

    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Lista todas las mascotas del cuidador
     */
    public function index()
    {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        $mascotaModel = new Mascota($this->db);

        $id_cuidador = $_SESSION['id_perfil'];

        $mascotas = $mascotaModel->obtenerMascotasPorCuidador($id_cuidador);
        $especies = $mascotaModel->obtenerEspecies();
        $colores = $mascotaModel->obtenerColores();
        $perfil_info = $mascotaModel->obtenerInfoCuidador($id_cuidador);

        $especies = is_array($especies) ? $especies : [];
        $colores = is_array($colores) ? $colores : [];
        $perfil_info = is_array($perfil_info) ? $perfil_info : [];

        include_once APP_PATH . '/views/cuidador/mis_mascotas.php';
    }

    /**
     * Ver detalle de una mascota específica
     */
    public function ver()
    {
        if (!isset($_GET['id'])) {
            header("Location: " . URL_BASE . "/cuidador/mis-mascotas");
            exit();
        }

        $id_mascota = $_GET['id'];
        $mascotaModel = new Mascota($this->db);
        $mascotaModel->id_mascota = $id_mascota;

        $datos = $mascotaModel->obtenerPerfilCompleto();

        // SEGURIDAD: Validar que la mascota pertenezca al cuidador en sesión
        if (!$datos || $datos['ID_Cuidador'] != $_SESSION['id_perfil']) {
            header("Location: " . URL_BASE . "/cuidador/mis-mascotas?error=acceso_denegado");
            exit();
        }

        $historial = $mascotaModel->verHistorialMedico();
        $perfil_info = $mascotaModel->obtenerInfoCuidador($_SESSION['id_perfil']);

        include_once APP_PATH . '/views/cuidador/mascota.php';
    }

    /**
     * Registrar mascota desde el cuidador (Preregistro)
     */
    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            return;
        }

        $mascota = new Mascota($this->db);
        $mascota->nombre = $_POST['nombre'] ?? '';
        $mascota->id_especie = $_POST['id_especie'] ?? 0;
        $mascota->id_color = $_POST['id_color'] ?? null;
        $mascota->sexo = $_POST['sexo'] ?? '';
        $mascota->rasgos = $_POST['rasgos'] ?? '';
        $mascota->estado_esterilizacion = $_POST['esterilizacion'] ?? '';
        $mascota->id_cuidador = $_SESSION['id_perfil'];

        // Datos por defecto (para ser llenados por veterinario)
        $mascota->raza = 'Pendiente';
        $mascota->edad = 0;
        $mascota->peso = 0;

        // 1. Intentar el Preregistro en la Base de Datos primero
        if ($mascota->registrarMascota()) {
            $id_nueva = $mascota->id_mascota;

            // 2. Si hay una foto, procesarla usando el ID generado
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $fileSize = $_FILES['foto']['size'];
                $fileType = $_FILES['foto']['type'];

                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Validación de extensión y tamaño (2MB)
                if (in_array($ext, $allowedExtensions) && $fileSize <= 2 * 1024 * 1024) {
                    $uploadPath = APP_PATH . '/public/uploads/pets/' . $id_nueva . '.' . $ext;

                    // Si ya existía una foto con otra extensión, borrarla para evitar duplicidad
                    $otra_ext = ($ext === 'png') ? 'jpg' : 'png';
                    $otra_ruta = APP_PATH . '/public/uploads/pets/' . $id_nueva . '.' . $otra_ext;
                    if (file_exists($otra_ruta))
                        @unlink($otra_ruta);
                    if ($ext === 'png') {
                        if (file_exists(APP_PATH . '/public/uploads/pets/' . $id_nueva . '.jpeg'))
                            @unlink(APP_PATH . '/public/uploads/pets/' . $id_nueva . '.jpeg');
                    }

                    move_uploaded_file($fileTmpPath, $uploadPath);
                }
            }

            echo json_encode(['status' => 'success', 'id' => $id_nueva]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo completar el preregistro de la mascota.']);
        }
    }
    /**
     * Actualizar solo la foto de la mascota
     */
    public function actualizarFoto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            return;
        }

        $id_mascota = $_POST['id_mascota'] ?? null;
        if (!$id_mascota) {
            echo json_encode(['status' => 'error', 'message' => 'ID de mascota no proporcionado']);
            return;
        }

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $fileName = $_FILES['foto']['name'];
            $fileSize = $_FILES['foto']['size'];

            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExtensions) && $fileSize <= 2 * 1024 * 1024) {
                // Limpiar archivos anteriores con diferentes extensiones para este mismo ID
                foreach (['jpg', 'jpeg', 'png'] as $e) {
                    $ruta = APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pets' . DIRECTORY_SEPARATOR . $id_mascota . '.' . $e;
                    if (file_exists($ruta))
                        @unlink($ruta);
                }

                $uploadPath = APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pets' . DIRECTORY_SEPARATOR . $id_mascota . '.' . $ext;

                if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                    echo json_encode(['status' => 'success', 'message' => 'Foto actualizada correctamente']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo al servidor']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Formato no permitido o archivo muy pesado (>2MB)']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se seleccionó ninguna imagen válida']);
        }
    }
}
?>