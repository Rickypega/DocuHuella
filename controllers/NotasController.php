<?php
// controllers/NotasController.php
// API JSON para el sistema de Mis Notas (solo acepta POST)

require_once APP_PATH . '/models/Nota.php';

class NotasController {

    public function handle() {
        // Asegurar sesión activa
        if (!isset($_SESSION['id_usuario'])) {
            http_response_code(401);
            echo json_encode(['exito' => false, 'mensaje' => 'No autenticado']);
            exit();
        }

        // Solo aceptar POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
            exit();
        }

        header('Content-Type: application/json; charset=utf-8');

        $database         = new Database();
        $db               = $database->getConnection();
        $nota             = new Nota($db);
        $nota->id_usuario = $_SESSION['id_usuario'];

        $action = isset($_POST['action']) ? trim($_POST['action']) : '';

        switch ($action) {

            // --------------------------------------------------------
            case 'obtener':
                $busqueda     = isset($_POST['busqueda'])     ? trim($_POST['busqueda'])     : '';
                $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
                $fecha_fin    = isset($_POST['fecha_fin'])    ? trim($_POST['fecha_fin'])    : '';

                $conditions = ['n.ID_Usuario = :id_usuario'];
                $params     = [':id_usuario' => $_SESSION['id_usuario']];

                if ($busqueda !== '') {
                    $conditions[] = "(n.Titulo LIKE :busqueda OR n.Contenido LIKE :busqueda)";
                    $params[':busqueda'] = '%' . $busqueda . '%';
                }
                if ($fecha_inicio !== '') {
                    $conditions[] = "DATE(n.Fecha_Creacion) >= :fecha_inicio";
                    $params[':fecha_inicio'] = $fecha_inicio;
                }
                if ($fecha_fin !== '') {
                    $conditions[] = "DATE(n.Fecha_Creacion) <= :fecha_fin";
                    $params[':fecha_fin'] = $fecha_fin;
                }

                $where = implode(' AND ', $conditions);
                $query = "SELECT n.*, m.Nombre AS Nombre_Mascota
                          FROM notas n
                          LEFT JOIN mascotas m ON n.ID_Mascota = m.ID_Mascota
                          WHERE {$where}
                          ORDER BY n.Fecha_Creacion DESC";

                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(['exito' => true, 'notas' => $notas]);
                break;

            // --------------------------------------------------------
            case 'crear':
                $titulo    = isset($_POST['titulo'])    ? trim($_POST['titulo'])    : '';
                $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
                $color     = isset($_POST['color'])     ? trim($_POST['color'])     : '#1A2D40';

                if ($titulo === '') {
                    echo json_encode(['exito' => false, 'mensaje' => 'El título es obligatorio']);
                    break;
                }

                $nota->titulo         = htmlspecialchars(strip_tags($titulo));
                $nota->contenido      = htmlspecialchars(strip_tags($contenido));
                $nota->color_etiqueta = htmlspecialchars(strip_tags($color));
                $nota->id_mascota     = null;

                if ($nota->crearNota()) {
                    echo json_encode(['exito' => true, 'id_nota' => $nota->id_nota, 'mensaje' => 'Nota creada correctamente']);
                } else {
                    echo json_encode(['exito' => false, 'mensaje' => 'Error al crear la nota']);
                }
                break;

            // --------------------------------------------------------
            case 'actualizar':
                $id_nota   = isset($_POST['id_nota'])   ? intval($_POST['id_nota'])   : 0;
                $titulo    = isset($_POST['titulo'])    ? trim($_POST['titulo'])    : '';
                $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
                $color     = isset($_POST['color'])     ? trim($_POST['color'])     : '#1A2D40';

                if ($id_nota <= 0 || $titulo === '') {
                    echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos']);
                    break;
                }

                $nota->id_nota        = $id_nota;
                $nota->titulo         = htmlspecialchars(strip_tags($titulo));
                $nota->contenido      = htmlspecialchars(strip_tags($contenido));
                $nota->color_etiqueta = htmlspecialchars(strip_tags($color));

                if ($nota->actualizarNota()) {
                    echo json_encode(['exito' => true, 'mensaje' => 'Nota actualizada correctamente']);
                } else {
                    echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar la nota']);
                }
                break;

            // --------------------------------------------------------
            case 'eliminar':
                $id_nota = isset($_POST['id_nota']) ? intval($_POST['id_nota']) : 0;

                if ($id_nota <= 0) {
                    echo json_encode(['exito' => false, 'mensaje' => 'ID de nota inválido']);
                    break;
                }

                $nota->id_nota = $id_nota;

                if ($nota->eliminarNota()) {
                    echo json_encode(['exito' => true, 'mensaje' => 'Nota eliminada correctamente']);
                } else {
                    echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar la nota']);
                }
                break;

            // --------------------------------------------------------
            case 'eliminarTodas':
                $password_ingresada = isset($_POST['password']) ? $_POST['password'] : '';

                if ($password_ingresada === '') {
                    echo json_encode(['exito' => false, 'mensaje' => 'Debes ingresar tu contraseña']);
                    break;
                }

                // Verificar contraseña del usuario en sesión
                $stmtPass = $db->prepare("SELECT Contrasena FROM usuarios WHERE ID_Usuario = :id");
                $stmtPass->bindParam(':id', $_SESSION['id_usuario']);
                $stmtPass->execute();
                $hash = $stmtPass->fetchColumn();

                if (!password_verify($password_ingresada, $hash)) {
                    echo json_encode(['exito' => false, 'mensaje' => 'Contraseña incorrecta. No se eliminaron las notas.']);
                    break;
                }

                if ($nota->eliminarTodasPorUsuario()) {
                    echo json_encode(['exito' => true, 'mensaje' => 'Todas las notas han sido eliminadas correctamente.']);
                } else {
                    echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar las notas.']);
                }
                break;

            // --------------------------------------------------------
            default:
                echo json_encode(['exito' => false, 'mensaje' => 'Acción no válida']);
        }

        exit();
    }
}
