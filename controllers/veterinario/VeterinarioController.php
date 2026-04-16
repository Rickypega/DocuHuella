<?php
require_once APP_PATH . '/config/auth_check.php';

class VeterinarioController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        $id_vet = $_SESSION['id_perfil'];

        // 1. Totales filtrados por veterinario
        // Mascotas atendidas (distintas)
        $query_mascotas = "SELECT COUNT(DISTINCT e.ID_Mascota) 
                           FROM consultas c 
                           JOIN expedientes e ON c.ID_Expediente = e.ID_Expediente 
                           WHERE c.ID_Veterinario = :id_v";
        $stmt = $this->db->prepare($query_mascotas);
        $stmt->execute([':id_v' => $id_vet]);
        $total_mascotas = $stmt->fetchColumn();

        // Consultas realizadas
        $query_consultas = "SELECT COUNT(*) FROM consultas WHERE ID_Veterinario = :id_v";
        $stmt = $this->db->prepare($query_consultas);
        $stmt->execute([':id_v' => $id_vet]);
        $total_consultas = $stmt->fetchColumn();

        // 2. Citas para el calendario
        $query_citas = "SELECT c.*, m.Nombre as Nombre_Mascota, cli.Nombre_Sucursal as Clinica, 
                               v.Nombre as Nombre_Vet, v.Apellido as Apellido_Vet
                        FROM citas c
                        JOIN mascotas m ON c.ID_Mascota = m.ID_Mascota
                        JOIN clinicas cli ON c.ID_Clinica = cli.ID_Clinica
                        JOIN veterinarios v ON c.ID_Veterinario = v.ID_Veterinario
                        WHERE c.ID_Veterinario = :id_v";
        $stmt = $this->db->prepare($query_citas);
        $stmt->execute([':id_v' => $id_vet]);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Incluir la vista
        include_once APP_PATH . '/views/veterinario/dashboard.php';
    }

    public function pacientes() {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        $id_vet = $_SESSION['id_perfil'];

        // Obtener lista de pacientes atendidos por este veterinario
        // Agregamos Cedula del cuidador
        $query = "SELECT DISTINCT m.*, e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza, 
                                 cui.Nombre AS Nombre_Cuidador, cui.Apellido AS Apellido_Cuidador, cui.Cedula AS Cedula_Cuidador,
                                 (SELECT MAX(Fecha_Consulta) FROM consultas con 
                                  JOIN expedientes exp ON con.ID_Expediente = exp.ID_Expediente 
                                  WHERE exp.ID_Mascota = m.ID_Mascota) as Ultima_Consulta
                  FROM mascotas m
                  JOIN expedientes exp ON m.ID_Mascota = exp.ID_Mascota
                  LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                  JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  WHERE m.ID_Mascota IN (
                      SELECT ID_Mascota FROM expedientes exp2
                      JOIN consultas con ON exp2.ID_Expediente = con.ID_Expediente
                      WHERE con.ID_Veterinario = :id_v
                      UNION
                      SELECT ID_Mascota FROM vacunaciones
                      WHERE ID_Veterinario = :id_v
                  )
                  ORDER BY m.Nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_v' => $id_vet]);
        $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once APP_PATH . '/views/veterinario/pacientes.php';
    }

    public function pacienteVer() {
        if (!isset($_GET['id']) || !isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/veterinario/pacientes");
            exit();
        }

        require_once APP_PATH . '/models/Mascota.php';
        $mascotaModel = new Mascota($this->db);
        $mascotaModel->id_mascota = $_GET['id'];
        $datos = $mascotaModel->obtenerPerfilCompleto();
        
        if (!$datos) {
            header("Location: " . URL_BASE . "/veterinario/pacientes?error=no_encontrado");
            exit();
        }

        $historial = $mascotaModel->verHistorialMedico();
        
        // Obtener Vacunas
        $stmt_v = $this->db->prepare("SELECT v.*, vac.Nombre_Vacuna 
                                    FROM vacunaciones v 
                                    JOIN vacunas vac ON v.ID_Vacuna = vac.ID_Vacuna 
                                    WHERE v.ID_Mascota = :id 
                                    ORDER BY v.Fecha_Aplicacion DESC");
        $stmt_v->execute([':id' => $_GET['id']]);
        $vacunas = $stmt_v->fetchAll(PDO::FETCH_ASSOC);

        // Catálogos para el modal de edición
        $especies = $mascotaModel->obtenerEspecies();
        $colores = $mascotaModel->obtenerColores();

        include_once APP_PATH . '/views/veterinario/paciente_ver.php';
    }

    public function consultas() {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        $id_vet = $_SESSION['id_perfil'];

        // Obtener historial de consultas realizadas por este veterinario
        // Agregamos nombre y cedula del cuidador
        $query = "SELECT con.*, m.Nombre as Nombre_Mascota, cli.Nombre_Sucursal as Clinica,
                         cui.Nombre AS Nombre_Cuidador, cui.Apellido AS Apellido_Cuidador, cui.Cedula AS Cedula_Cuidador
                  FROM consultas con
                  JOIN expedientes exp ON con.ID_Expediente = exp.ID_Expediente
                  JOIN mascotas m ON exp.ID_Mascota = m.ID_Mascota
                  JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  JOIN veterinarios v ON con.ID_Veterinario = v.ID_Veterinario
                  JOIN clinicas cli ON v.ID_Clinica = cli.ID_Clinica
                  WHERE con.ID_Veterinario = :id_v
                  ORDER BY con.Fecha_Consulta DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_v' => $id_vet]);
        $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Para el modal de nueva consulta: lista de mascotas atendidas o con expediente activo
        $query_m = "SELECT m.ID_Mascota, m.Nombre FROM mascotas m 
                    JOIN expedientes e ON m.ID_Mascota = e.ID_Mascota 
                    WHERE e.Estado_Expediente = 'Activo'";
        $mascotas_para_consulta = $this->db->query($query_m)->fetchAll(PDO::FETCH_ASSOC);

        include_once APP_PATH . '/views/veterinario/consultas.php';
    }

    public function registrarConsulta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/veterinario/consultas");
            exit();
        }

        require_once APP_PATH . '/models/Consulta.php';
        require_once APP_PATH . '/models/Expediente.php';
        
        $con = new Consulta($this->db);
        $expModel = new Expediente($this->db);
        
        $id_mascota = $_POST['id_mascota'];
        $expediente = $expModel->obtenerPorMascota($id_mascota);
        
        // Si no tiene expediente, lo creamos automáticamente para la clínica del veterinario
        if (!$expediente) {
            $expModel->id_mascota = $id_mascota;
            $expModel->id_clinica = $_SESSION['id_clinica'];
            if($expModel->crearExpediente()) {
                $expediente = $expModel->obtenerPorMascota($id_mascota);
            } else {
                header("Location: " . URL_BASE . "/veterinario/consultas?error=expediente_fail");
                exit();
            }
        }

        $con->id_expediente = $expediente['ID_Expediente'];
        $con->id_veterinario = $_SESSION['id_perfil'];
        $con->motivo_consulta = $_POST['motivo'];
        $con->sintomas = $_POST['sintomas'];
        $con->peso_kg = $_POST['peso'];
        $con->temperatura_c = $_POST['temperatura'];
        $con->frecuencia_cardiaca = $_POST['frecuencia'];
        $con->diagnostico = $_POST['diagnostico'];
        $con->tratamiento_sugerido = $_POST['tratamiento'];
        $con->observaciones_privadas = $_POST['observaciones'] ?? '';

        if ($con->registrarConsulta()) {
            $id_consulta = $con->id_consulta;
            
            // Actualizar peso maestro de la mascota automáticamente
            $stmt_w = $this->db->prepare("UPDATE mascotas SET Peso = :peso WHERE ID_Mascota = :id_m");
            $stmt_w->execute([':peso' => $_POST['peso'], ':id_m' => $id_mascota]);

            // Manejo de evidencias (fotos)
            if (isset($_FILES['evidencias']) && !empty($_FILES['evidencias']['name'][0])) {
                $total_size = array_sum($_FILES['evidencias']['size']);
                $max_size = 20 * 1024 * 1024; // 20MB limit

                if ($total_size <= $max_size) {
                    $upload_dir = APP_PATH . '/public/uploads/consultas/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    foreach ($_FILES['evidencias']['tmp_name'] as $key => $tmp_name) {
                        $file_name = $_FILES['evidencias']['name'][$key];
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                        
                        if (in_array($ext, $allowed)) {
                            $suffix = ($key === 0) ? "" : "_" . $key;
                            $new_name = $id_consulta . $suffix . "." . $ext;
                            move_uploaded_file($tmp_name, $upload_dir . $new_name);
                        }
                    }
                }
            }
            header("Location: " . URL_BASE . "/veterinario/consultas?success=1");
        } else {
            header("Location: " . URL_BASE . "/veterinario/consultas?error=db");
        }
    }

    public function consultaDetalle() {
        if (!isset($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Falta ID']);
            return;
        }

        require_once APP_PATH . '/models/Consulta.php';
        $con = new Consulta($this->db);
        $detalle = $con->obtenerDetallesCompletos($_GET['id']);
        
        if ($detalle) {
            // Buscar evidencias en carpeta
            $evidencias = [];
            $dir = APP_PATH . '/public/uploads/consultas/';
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $f) {
                    if (strpos($f, $_GET['id'] . "_") === 0 || strpos($f, $_GET['id'] . ".") === 0) {
                        $evidencias[] = URL_BASE . "/public/uploads/consultas/" . $f;
                    }
                }
            }
            $detalle['evidencias'] = $evidencias;
            echo json_encode(['status' => 'success', 'data' => $detalle]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No encontrado']);
        }
    }

    public function citas() {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        require_once APP_PATH . '/models/Cita.php';
        $citaModel = new Cita($this->db);
        $citas = $citaModel->obtenerAgendaVeterinario($_SESSION['id_perfil']);
        
        // Consultar nuevamente para obtener cedula y peso que no vienen en obtenerAgendaVeterinario original si es necesario
        // O mejor, modificar obtenerAgendaVeterinario o hacer una personalizada aqui.
        $query = "SELECT c.*, m.Nombre AS Nombre_Mascota, m.Peso, e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza,
                         cui.Nombre AS Nombre_Cuidador, cui.Apellido AS Apellido_Cuidador, cui.Cedula, cui.Telefono
                  FROM citas c
                  JOIN mascotas m ON c.ID_Mascota = m.ID_Mascota
                  LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                  LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                  JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  WHERE c.ID_Veterinario = :id_v
                  ORDER BY c.Fecha_Cita DESC, c.Hora_Cita DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_v' => $_SESSION['id_perfil']]);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mascotas para agendar
        $mascotas = $this->db->query("SELECT ID_Mascota, Nombre FROM mascotas")->fetchAll(PDO::FETCH_ASSOC);

        include_once APP_PATH . '/views/veterinario/citas.php';
    }

    public function agendarCita() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        require_once APP_PATH . '/models/Cita.php';
        $cita = new Cita($this->db);
        $cita->id_veterinario = $_SESSION['id_perfil'];
        $cita->id_clinica = $_SESSION['id_clinica'];
        $cita->id_mascota = $_POST['id_mascota'];
        $cita->fecha_cita = $_POST['fecha'];
        $cita->hora_cita = $_POST['hora'];
        $cita->motivo = $_POST['motivo'];
        $cita->notas = $_POST['notas'] ?? '';
        
        if ($cita->agendarCita()) {
            header("Location: " . URL_BASE . "/veterinario/citas?success=1");
        } else {
            header("Location: " . URL_BASE . "/veterinario/citas?error=1");
        }
    }

    public function editarCita() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        require_once APP_PATH . '/models/Cita.php';
        $cita = new Cita($this->db);
        if ($cita->actualizarFecha($_POST['id_cita'], $_POST['fecha'], $_POST['hora'])) {
            header("Location: " . URL_BASE . "/veterinario/citas?success=edit");
        } else {
            header("Location: " . URL_BASE . "/veterinario/citas?error=edit");
        }
    }

    public function eliminarCita() {
        if (!isset($_GET['id'])) return;
        require_once APP_PATH . '/models/Cita.php';
        $cita = new Cita($this->db);
        $cita->eliminar($_GET['id']);
        header("Location: " . URL_BASE . "/veterinario/citas?success=delete");
    }

    // --- NUEVAS FUNCIONALIDADES (REFUERZOS Y LOOKUP) ---

    public function buscarCuidadorPorCedula() {
        if (!isset($_GET['cedula'])) {
            echo json_encode(['status' => 'error', 'message' => 'Cédula requerida']);
            return;
        }

        $cedula = $_GET['cedula'];
        
        // 1. Buscar Cuidador
        $query_c = "SELECT ID_Cuidador, Nombre, Apellido FROM cuidadores WHERE Cedula = :ced LIMIT 1";
        $stmt_c = $this->db->prepare($query_c);
        $stmt_c->execute([':ced' => $cedula]);
        $cuidador = $stmt_c->fetch(PDO::FETCH_ASSOC);

        if (!$cuidador) {
            echo json_encode(['status' => 'error', 'message' => 'Cuidador no encontrado']);
            return;
        }

        // 2. Buscar mascotas de ese cuidador
        $query_m = "SELECT ID_Mascota, Nombre FROM mascotas WHERE ID_Cuidador = :id_c";
        $stmt_m = $this->db->prepare($query_m);
        $stmt_m->execute([':id_c' => $cuidador['ID_Cuidador']]);
        $mascotas = $stmt_m->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'cuidador' => $cuidador,
            'mascotas' => $mascotas
        ]);
    }

    public function vacunas() {
        if (!isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/login");
            exit();
        }

        $id_vet = $_SESSION['id_perfil'];

        // Últimas vacuanciones registradas por este veterinario
        $query = "SELECT v.*, m.Nombre AS Nombre_Mascota, vacu.Nombre_Vacuna, vacu.Periodo_Refuerzo_Meses,
                         cui.Nombre AS Nombre_Cui, cui.Apellido AS Apellido_Cui
                  FROM vacunaciones v
                  JOIN mascotas m ON v.ID_Mascota = m.ID_Mascota
                  JOIN vacunas vacu ON v.ID_Vacuna = vacu.ID_Vacuna
                  JOIN cuidadores cui ON m.ID_Cuidador = cui.ID_Cuidador
                  WHERE v.ID_Veterinario = :id_v
                  ORDER BY v.Fecha_Aplicacion DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_v' => $id_vet]);
        $vacunaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Catálogo de vacunas para el modal
        $query_cat = "SELECT * FROM vacunas ORDER BY Nombre_Vacuna ASC";
        $catalogo_vacunas = $this->db->query($query_cat)->fetchAll(PDO::FETCH_ASSOC);

        include_once APP_PATH . '/views/veterinario/vacunas.php';
    }

    public function registrarVacunacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_perfil'])) {
            header("Location: " . URL_BASE . "/veterinario/vacunas");
            exit();
        }

        require_once APP_PATH . '/models/Vacunacion.php';
        require_once APP_PATH . '/models/Cita.php';

        $v = new Vacunacion($this->db);
        $v->id_mascota = $_POST['id_mascota'];
        $v->id_vacuna = $_POST['id_vacuna'];
        $v->id_veterinario = $_SESSION['id_perfil'];
        $v->fecha_aplicacion = $_POST['fecha_aplicacion'];
        $v->fecha_refuerzo = !empty($_POST['fecha_refuerzo']) ? $_POST['fecha_refuerzo'] : null;
        $v->lote_vacuna = $_POST['lote'] ?? '';
        $v->observaciones = $_POST['observaciones'] ?? '';

        // Asegurar que existe un expediente para esta mascota en esta clínica
        require_once APP_PATH . '/models/Expediente.php';
        $expedienteModel = new Expediente($this->db);
        $id_clinica = $_SESSION['id_clinica']; // El veterinario está logueado en una clínica
        $id_expediente = $expedienteModel->obtenerOcrearExpediente($v->id_mascota, $id_clinica);

        if ($v->registrarVacunacion()) {
            
            // Si es refuerzo o se eligió una fecha futura, crear cita automática para el cuidador
            if ($v->fecha_refuerzo) {
                // Obtener nombre de la mascota y de la vacuna para la nota
                $stmt_m = $this->db->prepare("SELECT Nombre FROM mascotas WHERE ID_Mascota = :id");
                $stmt_m->execute([':id' => $v->id_mascota]);
                $nombre_mascota = $stmt_m->fetchColumn();

                $stmt_vac = $this->db->prepare("SELECT Nombre_Vacuna FROM vacunas WHERE ID_Vacuna = :id");
                $stmt_vac->execute([':id' => $v->id_vacuna]);
                $nombre_vacuna = $stmt_vac->fetchColumn();

                $cita = new Cita($this->db);
                $cita->id_clinica = $_SESSION['id_clinica'];
                $cita->id_veterinario = null; // No aparece en el calendario del vet
                $cita->id_mascota = $v->id_mascota;
                $cita->fecha_cita = $v->fecha_refuerzo;
                $cita->hora_cita = '08:00:00'; // Hora por defecto para recordatorio
                $cita->motivo = "Refuerzo de Vacuna: " . $nombre_vacuna;
                $cita->notas = "Fecha para refuerzo de la vacuna $nombre_vacuna para la mascota $nombre_mascota, recuerda agendar una cita con un tu veterinario de confianza lo antes posible";
                
                $cita->agendarCita();
            }

            header("Location: " . URL_BASE . "/veterinario/vacunas?success=1");
        } else {
            header("Location: " . URL_BASE . "/veterinario/vacunas?error=db");
        }
    }

    public function actualizarPaciente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        require_once APP_PATH . '/models/Mascota.php';
        $mascota = new Mascota($this->db);
        if ($mascota->actualizarDatosVete($_POST['id_mascota'], $_POST)) {
            header("Location: " . URL_BASE . "/veterinario/paciente/ver?id=" . $_POST['id_mascota'] . "&success=update");
        } else {
            header("Location: " . URL_BASE . "/veterinario/paciente/ver?id=" . $_POST['id_mascota'] . "&error=update");
        }
    }

    public function obtenerRazas() {
        if (!isset($_GET['id_especie'])) {
            echo json_encode([]);
            exit();
        }
        $id_especie = $_GET['id_especie'];
        // Usar capitalización exacta de la tabla según el SQL
        $stmt = $this->db->prepare("SELECT ID_Raza, Nombre_Raza FROM Razas WHERE ID_Especie = :id ORDER BY Nombre_Raza ASC");
        $stmt->execute([':id' => $id_especie]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($res);
        exit();
    }

    public function vacunaDetalle() {
        if (!isset($_GET['id'])) return;
        $id = $_GET['id'];
        $query = "SELECT v.*, vac.Nombre_Vacuna, vac.Descripcion, m.Nombre AS Nombre_Mascota, 
                         vet.Nombre AS Nombre_Vet, vet.Apellido AS Apellido_Vet
                  FROM vacunaciones v 
                  JOIN vacunas vac ON v.ID_Vacuna = vac.ID_Vacuna 
                  JOIN mascotas m ON v.ID_Mascota = m.ID_Mascota
                  JOIN veterinarios vet ON v.ID_Veterinario = vet.ID_Veterinario
                  WHERE v.ID_Vacunacion = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($detalle) {
            echo json_encode(['status' => 'success', 'data' => $detalle]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No encontrada']);
        }
        exit();
    }

    public function exportarExpediente() {
        if (!isset($_GET['id'])) return;
        while (ob_get_level()) ob_end_clean();
        ob_start();
        $id_mascota = $_GET['id'];
        
        require_once APP_PATH . '/models/Mascota.php';
        $m = new Mascota($this->db);
        $m->id_mascota = $id_mascota;
        $datos = $m->obtenerPerfilCompleto();
        $consultas = $m->verHistorialMedico();
        
        // Vacunas
        $stmt_v = $this->db->prepare("SELECT v.*, vac.Nombre_Vacuna FROM vacunaciones v JOIN vacunas vac ON v.ID_Vacuna = vac.ID_Vacuna WHERE v.ID_Mascota = :id ORDER BY v.Fecha_Aplicacion DESC");
        $stmt_v->execute([':id' => $id_mascota]);
        $vacunas = $stmt_v->fetchAll(PDO::FETCH_ASSOC);

        require_once APP_PATH . '/libs/fpdf.php';
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Cabecera Institucional
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(26, 45, 64); // dh-navy
        $pdf->Cell(0, 15, utf8_decode('EXPEDIENTE CLÍNICO DIGITAL'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 5, utf8_decode('DocuHuella - Gestión Médica Veterinaria'), 0, 1, 'C');
        $pdf->Ln(10);

        // Bloque 1: Datos de la Mascota y Cuidador
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('1. IDENTIFICACIÓN COMPLETA'), 0, 1, 'L', true);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, utf8_decode('PACIENTE:'), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, utf8_decode('Nombre: ' . $datos['Nombre']), 0, 0); 
        $pdf->Cell(0, 6, utf8_decode('Especie/Raza: ' . $datos['Especie'] . ' / ' . ($datos['Raza'] ?? 'N/A')), 0, 1);
        $pdf->Cell(50, 6, utf8_decode('Sexo/Edad: ' . ($datos['Sexo']=='M'?'Macho':'Hembra') . ' / ' . $datos['Edad'] . ' años'), 0, 0);
        $pdf->Cell(0, 6, utf8_decode('Peso: ' . $datos['Peso'] . ' kg'), 0, 1);
        
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, utf8_decode('CUIDADOR RESPONSABLE:'), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, utf8_decode('Nombre: ' . $datos['Nombre_Cuidador'] . ' ' . ($datos['App_Cuidador'] ?? '')), 0, 0);
        $pdf->Cell(0, 6, utf8_decode('Cédula: ' . ($datos['Cedula'] ?? 'N/A')), 0, 1);
        $pdf->Cell(0, 6, utf8_decode('Teléfono: ' . ($datos['Telefono'] ?? 'N/A')), 0, 1);
        $pdf->Ln(5);

        // Bloque 2: Historial de Consultas
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('2. HISTORIAL DE CONSULTAS MÉDICAS'), 0, 1, 'L', true);
        if (empty($consultas)) {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, utf8_decode('No hay consultas registradas.'), 0, 1);
        } else {
            $count = 1;
            foreach ($consultas as $c) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 8, utf8_decode($count . '. CONSULTA - FECHA: ' . date('d/m/Y', strtotime($c['Fecha_Consulta'])) . ' - MOTIVO: ' . $c['Motivo']), 'T', 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, utf8_decode('   DIAGNÓSTICO: ' . ($c['Diagnostico'] ?: 'Sin registro')));
                $pdf->MultiCell(0, 5, utf8_decode('   TRATAMIENTO: ' . ($c['Tratamiento_Recomendado'] ?: 'Sin registro')));
                $pdf->MultiCell(0, 5, utf8_decode('   CENTRO: ' . $c['Clinica'] . ' (Dr/a. ' . $c['Nombre_Vet'] . ')'));
                $pdf->Ln(2);
                $count++;
            }
        }
        $pdf->Ln(5);

        // Bloque 3: Registro de Inmunización (Vacunas)
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

    public function exportarConsulta() {
        if (!isset($_GET['id']) || !isset($_GET['formato'])) return;
        
        $id_consulta = $_GET['id'];
        $formato = $_GET['formato'];
        
        require_once APP_PATH . '/models/Consulta.php';
        $con = new Consulta($this->db);
        $d = $con->obtenerDetallesCompletos($id_consulta);
        
        if (!$d) die("Consulta no encontrada");

        // Buscar evidencias
        $evidencias = [];
        $dir = APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'consultas' . DIRECTORY_SEPARATOR;
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $f) {
                // Solo archivos que empiecen con ID exacto seguido de . o _ (evita que el id "1" coincida con "10")
                if (preg_match("/^" . $id_consulta . "[\._]/", $f)) {
                    $path = realpath($dir . $f);
                    if ($path) $evidencias[] = $path;
                }
            }
        }

        if ($formato === 'pdf') {
            while (ob_get_level()) ob_end_clean();
            ob_start();
            require_once APP_PATH . '/libs/fpdf.php';
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, utf8_decode('Reporte Médico - DocuHuella'), 0, 1, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(230,230,230);
            $pdf->Cell(0, 8, utf8_decode('1. Información de la Mascota'), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, utf8_decode('Nombre: ' . $d['Nombre_Mascota']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Especie/Raza: ' . $d['Especie'] . ' / ' . $d['Raza']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Edad: ' . $d['Edad_Mascota'] . ' años | Sexo: ' . ($d['Sexo_Mascota']=='M'?'Macho':'Hembra')), 0, 1);
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode('2. Información del Cuidador'), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, utf8_decode('Nombre: ' . $d['Nombre_Cuidador'] . ' ' . $d['Apellido_Cuidador']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Cédula: ' . $d['Cedula_Cuidador']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Teléfono: ' . $d['Telefono_Cuidador']), 0, 1);
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode('3. Información de la Consulta'), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y h:i A', strtotime($d['Fecha_Consulta']))), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Motivo: ' . $d['Motivo_Consulta']), 0, 1);
            $pdf->MultiCell(0, 6, utf8_decode('Sintomas: ' . $d['Sintomas']));
            $pdf->Cell(0, 6, utf8_decode('Constantes: Peso ' . $d['Peso_KG'] . 'kg | Temp ' . $d['Temperatura_C'] . 'C | FC ' . $d['Frecuencia_Cardiaca'] . 'bpm'), 0, 1);
            $pdf->MultiCell(0, 6, utf8_decode('Diagnóstico: ' . $d['Diagnostico']));
            $pdf->MultiCell(0, 6, utf8_decode('Tratamiento: ' . $d['Tratamiento_Sugerido']));
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode('4. Información del Veterinario / Clínica'), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, utf8_decode('Médico: ' . $d['Nombre_Vet'] . ' ' . $d['Apellido_Vet']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Clínica: ' . $d['Clinica']), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('RNC: ' . $d['RNC_Clinica']), 0, 1);
            $pdf->Ln(10);

            if (!empty($evidencias)) {
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetFillColor(230,230,230);
                $pdf->Cell(0, 8, utf8_decode('Evidencias Fotográficas'), 0, 1, 'L', true);
                $pdf->Ln(5);

                foreach ($evidencias as $img) {
                    if (!is_file($img)) continue;
                    
                    $temp_file = null;
                    $render_img = $img;
                    $type = '';
                    
                    // Carga universal con GD (si está disponible)
                    $img_data = @file_get_contents($img);
                    if ($img_data && function_exists('imagecreatefromstring')) {
                        $gd_img = @imagecreatefromstring($img_data);
                        if ($gd_img) {
                            $type = 'JPEG';
                            $temp_file = tempnam(sys_get_temp_dir(), 'dh_') . '.jpg';
                            if (@imagejpeg($gd_img, $temp_file, 80)) {
                                $render_img = $temp_file;
                            } else {
                                $type = '';
                            }
                            @imagedestroy($gd_img);
                        }
                    }

                    // Fallback nativo
                    if (!$type) {
                        $info = @getimagesize($img);
                        if ($info) {
                            $mime = $info['mime'];
                            if (strpos($mime, 'png') !== false) $type = 'PNG';
                            elseif (strpos($mime, 'jpeg') !== false) $type = 'JPEG';
                        }
                    }
                    
                    if ($type && file_exists($render_img)) {
                        if ($pdf->GetY() > 210) $pdf->AddPage();
                        $pdf->Image($render_img, 15, $pdf->GetY(), 110, 0, $type);
                        $pdf->Ln(75); 
                    } else {
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->Cell(0, 5, utf8_decode('No se pudo procesar imagen: ' . basename($img)), 0, 1);
                        $pdf->Ln(2);
                    }

                    if ($temp_file && file_exists($temp_file)) {
                        @unlink($temp_file);
                    }
                }
            } else {
                $pdf->SetFont('Arial', 'I', 10);
                $pdf->Cell(0, 8, utf8_decode('No se adjuntaron evidencias para esta consulta.'), 0, 1);
            }

            $pdf->Output('D', 'Reporte_Medico_' . $d['Nombre_Mascota'] . '_' . date('Ymd') . '.pdf');
            exit();

        } elseif ($formato === 'excel') {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Reporte_Consultas_" . date('Ymd_Hi') . ".xls");
            
            echo "\xEF\xBB\xBF"; 
            echo "<table border='1'>";
            echo "<tr><th colspan='2' style='background:#1A2D40; color:white; font-size:18px;'>REPORTE DE CONSULTA MÉDICA</th></tr>";
            
            echo "<tr><td colspan='2' style='background:#C5AA7F; font-weight:bold;'>1. DETALLE DE MASCOTA</td></tr>";
            echo "<tr><td>Nombre:</td><td>" . $d['Nombre_Mascota'] . "</td></tr>";
            echo "<tr><td>Especie / Raza:</td><td>" . $d['Especie'] . " / " . $d['Raza'] . "</td></tr>";
            
            echo "<tr><td colspan='2' style='background:#C5AA7F; font-weight:bold;'>2. DETALLE DE CUIDADOR</td></tr>";
            echo "<tr><td>Nombre:</td><td>" . $d['Nombre_Cuidador'] . " " . $d['Apellido_Cuidador'] . "</td></tr>";
            echo "<tr><td>Cedula:</td><td>" . $d['Cedula_Cuidador'] . "</td></tr>";

            echo "<tr><td colspan='2' style='background:#C5AA7F; font-weight:bold;'>3. DETALLE DE CONSULTA</td></tr>";
            echo "<tr><td>Fecha:</td><td>" . $d['Fecha_Consulta'] . "</td></tr>";
            echo "<tr><td>Motivo:</td><td>" . $d['Motivo_Consulta'] . "</td></tr>";
            echo "<tr><td>Diagnóstico:</td><td>" . $d['Diagnostico'] . "</td></tr>";

            if (!empty($evidencias)) {
                echo "<tr><td colspan='2' style='background:#C5AA7F; font-weight:bold;'>4. EVIDENCIAS FOTOGRÁFICAS</td></tr>";
                foreach ($evidencias as $e) {
                    $url = URL_BASE . "/public/uploads/consultas/" . basename($e);
                    echo "<tr><td>Foto:</td><td><img src='$url' width='150' height='150' style='display:block;'></td></tr>";
                }
            }
            
            echo "</table>";
            exit();
        }
    }
}

