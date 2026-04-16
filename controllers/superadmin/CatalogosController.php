<?php
require_once 'config/auth_check.php';
require_once 'models/Vacuna.php';
require_once 'models/Especie.php';
require_once 'models/Raza.php';
require_once 'models/Color.php';
require_once 'models/Especialidad.php';

class CatalogosController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        // Cargar todos los catálogos
        $vacunaModel = new Vacuna($this->db);
        $especieModel = new Especie($this->db);
        $razaModel = new Raza($this->db);
        $colorModel = new Color($this->db);
        $especialidadModel = new Especialidad($this->db);

        $vacunas = $vacunaModel->leerTodas();
        $especies = $especieModel->obtenerTodas();
        $razas = $razaModel->obtenerTodas();
        $colores = $colorModel->obtenerTodos();
        $especialidades = $especialidadModel->obtenerTodas();

        include_once APP_PATH . '/views/superadmin/catalogos.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $tipo = $_POST['tipo']; // vacunas, especies, razas, colores, especialidades
        $id = $_POST['id'] ?? null;
        $exito = false;

        switch ($tipo) {
            case 'vacunas':
                $obj = new Vacuna($this->db);
                $obj->id_vacuna = $id;
                $obj->nombre_vacuna = $_POST['nombre'];
                $obj->descripcion = $_POST['descripcion'];
                $obj->periodo_refuerzo_meses = $_POST['meses'];
                $exito = $id ? $obj->actualizar() : $obj->crearVacuna();
                break;

            case 'especies':
                $obj = new Especie($this->db);
                $obj->id_especie = $id;
                $obj->nombre_especie = $_POST['nombre'];
                $exito = $id ? $obj->actualizar() : $obj->crearEspecie();
                break;

            case 'razas':
                $obj = new Raza($this->db);
                $obj->id_raza = $id;
                $obj->id_especie = $_POST['id_especie'];
                $obj->nombre_raza = $_POST['nombre'];
                $exito = $id ? $obj->actualizar() : $obj->crearRaza();
                break;

            case 'colores':
                $obj = new Color($this->db);
                $obj->id_color = $id;
                $obj->nombre_color = $_POST['nombre'];
                $exito = $id ? $obj->actualizar() : $obj->crear();
                break;

            case 'especialidades':
                $obj = new Especialidad($this->db);
                $obj->id_especialidad = $id;
                $obj->nombre_especialidad = $_POST['nombre'];
                $exito = $id ? $obj->actualizar() : $obj->crear();
                break;
        }

        if ($exito) {
            header("Location: " . URL_BASE . "/superadmin/catalogos?success=1&tab=$tipo");
        } else {
            header("Location: " . URL_BASE . "/superadmin/catalogos?error=1&tab=$tipo");
        }
    }

    public function eliminar() {
        if (!isset($_GET['id']) || !isset($_GET['tipo'])) return;

        $id = $_GET['id'];
        $tipo = $_GET['tipo'];
        $exito = false;
        $tieneRelaciones = false;

        switch ($tipo) {
            case 'vacunas':
                $obj = new Vacuna($this->db);
                if ($obj->tieneRelaciones($id)) $tieneRelaciones = true;
                else $exito = $obj->eliminar($id);
                break;
            case 'especies':
                $obj = new Especie($this->db);
                if ($obj->tieneRelaciones($id)) $tieneRelaciones = true;
                else $exito = $obj->eliminar($id);
                break;
            case 'razas':
                $obj = new Raza($this->db);
                if ($obj->tieneRelaciones($id)) $tieneRelaciones = true;
                else $exito = $obj->eliminar($id);
                break;
            case 'colores':
                $obj = new Color($this->db);
                if ($obj->tieneRelaciones($id)) $tieneRelaciones = true;
                else $exito = $obj->eliminar($id);
                break;
            case 'especialidades':
                $obj = new Especialidad($this->db);
                if ($obj->tieneRelaciones($id)) $tieneRelaciones = true;
                else $exito = $obj->eliminar($id);
                break;
        }

        if ($tieneRelaciones) {
            header("Location: " . URL_BASE . "/superadmin/catalogos?error=restricted&tab=$tipo");
        } elseif ($exito) {
            header("Location: " . URL_BASE . "/superadmin/catalogos?success=deleted&tab=$tipo");
        } else {
            header("Location: " . URL_BASE . "/superadmin/catalogos?error=1&tab=$tipo");
        }
    }
}
?>
