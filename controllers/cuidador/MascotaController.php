<?php
require_once APP_PATH . '/models/Mascota.php';
require_once APP_PATH . '/models/Vacunacion.php';
require_once APP_PATH . '/models/Consulta.php';

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
        
        // Obtener historial de vacunas
        $vacunaModel = new Vacunacion($this->db);
        $vacunas = $vacunaModel->obtenerCartillaPorMascota($id_mascota);
        
        $perfil_info = $mascotaModel->obtenerInfoCuidador($_SESSION['id_perfil']);

        include_once APP_PATH . '/views/cuidador/mascota.php';
    }

    /**
     * Detalle de Consulta vía AJAX (Seguro para Cuidador)
     */
    public function consultaDetalle()
    {
        if (!isset($_GET['id']) || !isset($_SESSION['id_perfil'])) {
            echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
            return;
        }

        $id_consulta = $_GET['id'];
        $consultaModel = new Consulta($this->db);
        $detalle = $consultaModel->obtenerDetallesCompletos($id_consulta);

        // Seguridad: Verificar que el dueño de la mascota sea el usuario en sesión
        if (!$detalle || $detalle['ID_Cuidador'] != $_SESSION['id_perfil']) {
            echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para ver esta consulta']);
            return;
        }

        // Buscar evidencias
        $evidencias = [];
        $dir = APP_PATH . '/public/uploads/consultas/';
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $f) {
                if (strpos($f, $id_consulta . "_") === 0 || strpos($f, $id_consulta . ".") === 0) {
                    $evidencias[] = URL_BASE . "/public/uploads/consultas/" . $f;
                }
            }
        }
        $detalle['evidencias'] = $evidencias;

        echo json_encode(['status' => 'success', 'data' => $detalle]);
    }

    /**
     * Detalle de Vacuna vía AJAX (Seguro para Cuidador)
     */
    public function vacunaDetalle()
    {
        if (!isset($_GET['id']) || !isset($_SESSION['id_perfil'])) {
            echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
            return;
        }

        $id_vacunacion = $_GET['id'];
        
        // El veterinario tiene una query similar, la adaptamos para asegurar propiedad
        $query = "SELECT v.*, vac.Nombre_Vacuna, vac.Descripcion, m.Nombre AS Nombre_Mascota, m.ID_Cuidador,
                         vet.Nombre AS Nombre_Vet, vet.Apellido AS Apellido_Vet
                  FROM vacunaciones v 
                  JOIN vacunas vac ON v.ID_Vacuna = vac.ID_Vacuna 
                  JOIN mascotas m ON v.ID_Mascota = m.ID_Mascota
                  JOIN veterinarios vet ON v.ID_Veterinario = vet.ID_Veterinario
                  WHERE v.ID_Vacunacion = :id LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id_vacunacion]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detalle || $detalle['ID_Cuidador'] != $_SESSION['id_perfil']) {
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $detalle]);
    }

    /**
     * Exportar Expediente en PDF (Seguro para Cuidador)
     */
    public function exportarExpediente()
    {
        if (!isset($_GET['id']) || !isset($_SESSION['id_perfil'])) {
            die("Acceso denegado");
        }

        $id_mascota = $_GET['id'];
        $mascotaModel = new Mascota($this->db);
        $mascotaModel->id_mascota = $id_mascota;
        $datos = $mascotaModel->obtenerPerfilCompleto();

        // Seguridad
        if (!$datos || $datos['ID_Cuidador'] != $_SESSION['id_perfil']) {
            die("No tienes permiso para exportar este archivo.");
        }

        $consultas = $mascotaModel->verHistorialMedico();
        
        $vacunaModel = new Vacunacion($this->db);
        $vacunas = $vacunaModel->obtenerCartillaPorMascota($id_mascota);

        // Generar PDF
        while (ob_get_level()) ob_end_clean();
        ob_start();
        
        require_once APP_PATH . '/libs/fpdf.php';
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Cabecera
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(26, 45, 64);
        $pdf->Cell(0, 15, utf8_decode('EXPEDIENTE CLÍNICO DIGITAL'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 5, utf8_decode('DocuHuella - Gestión Médica Veterinaria'), 0, 1, 'C');
        $pdf->Ln(10);

        // Datos Mascota
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('1. IDENTIFICACIÓN DEL PACIENTE'), 0, 1, 'L', true);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Ln(2);
        $pdf->Cell(50, 7, utf8_decode('Nombre: ' . $datos['Nombre']), 0, 0);
        $pdf->Cell(0, 7, utf8_decode('Especie/Raza: ' . $datos['Especie'] . ' / ' . ($datos['Raza'] ?? 'N/A')), 0, 1);
        $pdf->Cell(50, 7, utf8_decode('Sexo/Edad: ' . ($datos['Sexo']=='M'?'Macho':'Hembra') . ' / ' . $datos['Edad'] . ' años'), 0, 0);
        $pdf->Cell(0, 7, utf8_decode('Peso/Color: ' . $datos['Peso'] . ' kg / ' . $datos['Color']), 0, 1);
        $pdf->MultiCell(0, 7, utf8_decode('Rasgos: ' . ($datos['Rasgos'] ?: 'Ninguno registrado')));
        $pdf->Ln(5);

        // Consultas
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('2. HISTORIAL DE CONSULTAS'), 0, 1, 'L', true);
        if (empty($consultas)) {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, utf8_decode('No hay consultas registradas.'), 0, 1);
        } else {
            foreach ($consultas as $c) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 8, utf8_decode('Fecha: ' . date('d/m/Y', strtotime($c['Fecha_Consulta'])) . ' - Motivo: ' . $c['Motivo']), 'T', 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, utf8_decode('Diagnóstico: ' . ($c['Diagnostico'] ?: 'N/A')));
                $pdf->MultiCell(0, 5, utf8_decode('Tratamiento: ' . ($c['Tratamiento_Recomendado'] ?: 'N/A')));
                $pdf->Cell(0, 5, utf8_decode('Centro: ' . $c['Clinica'] . ' (Dr. ' . $c['Nombre_Vet'] . ' ' . $c['Apellido_Vet'] . ')'), 0, 1);
                $pdf->Ln(2);
            }
        }
        $pdf->Ln(5);

        // Vacunas
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('3. REGISTRO DE VACUNACIÓN'), 0, 1, 'L', true);
        if (empty($vacunas)) {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, utf8_decode('No hay vacunas registradas.'), 0, 1);
        } else {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 8, utf8_decode('Fecha'), 1, 0, 'C');
            $pdf->Cell(80, 8, utf8_decode('Vacuna'), 1, 0, 'C');
            $pdf->Cell(70, 8, utf8_decode('Próx. Refuerzo'), 1, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            foreach ($vacunas as $v) {
                $pdf->Cell(40, 8, date('d/m/Y', strtotime($v['Fecha_Aplicacion'])), 1, 0, 'C');
                $pdf->Cell(80, 8, utf8_decode($v['Nombre_Vacuna']), 1, 0, 'L');
                $pdf->Cell(70, 8, ($v['Fecha_Refuerzo'] ? date('d/m/Y', strtotime($v['Fecha_Refuerzo'])) : 'N/A'), 1, 1, 'C');
            }
        }

        $pdf->Output('I', 'Expediente_' . $datos['Nombre'] . '.pdf');
        exit();
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