<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    if(!defined('URL_BASE')) { include_once __DIR__.'/../../config/db.php'; }
    header("Location: " . URL_BASE . "/login?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';

class DashboardController {
    
    public function ver() {
        $database = new Database();
        $db = $database->getConnection();
        
        $id_admin = $_SESSION['id_perfil'];

        // 1. CAPTURAR FILTROS (Si existen)
        $filtro_clinica = !empty($_GET['id_clinica']) ? $_GET['id_clinica'] : null;
        $fecha_inicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fecha_fin = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        try {
            // 2. OBTENER MIS CLÍNICAS PARA EL MENÚ DESPLEGABLE
            $stmtMisClinicas = $db->prepare("SELECT ID_Clinica, Nombre_Sucursal FROM Clinicas WHERE ID_Admin = :id_admin");
            $stmtMisClinicas->bindParam(':id_admin', $id_admin);
            $stmtMisClinicas->execute();
            $mis_clinicas = $stmtMisClinicas->fetchAll(PDO::FETCH_ASSOC);

            // 3. DETERMINAR EL TÍTULO DINÁMICO
            $titulo_dashboard = "Visión General";
            if ($filtro_clinica) {
                foreach ($mis_clinicas as $c) {
                    if ($c['ID_Clinica'] == $filtro_clinica) {
                        $titulo_dashboard = "Visión de " . $c['Nombre_Sucursal'];
                        break;
                    }
                }
            }

            // 4. CONSTRUIR CONDICIONES SQL DINÁMICAS
            // Filtro de sucursal: Si eligió una, usamos esa. Si no, usamos TODAS las del admin.
            $whereClinica = $filtro_clinica ? "ID_Clinica = :id_clinica" : "ID_Clinica IN (SELECT ID_Clinica FROM Clinicas WHERE ID_Admin = :id_admin)";
            
            // Filtro de fechas 
            $filtroFechasCita = ($fecha_inicio && $fecha_fin) ? " AND Fecha_Cita BETWEEN :inicio AND :fin" : "";
            $filtroFechasExp = ($fecha_inicio && $fecha_fin) ? " AND Fecha_Creacion BETWEEN :inicio AND :fin" : "";

            // --- EJECUCIÓN DE CONSULTAS ---

            // Sucursales
            $stmt = $db->prepare("SELECT COUNT(*) FROM Clinicas WHERE ID_Admin = :id_admin" . ($filtro_clinica ? " AND ID_Clinica = :id_clinica" : ""));
            $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            $stmt->execute();
            $total_sucursales = $stmt->fetchColumn();

            // Citas
            $stmt = $db->prepare("SELECT COUNT(*) FROM Citas WHERE $whereClinica $filtroFechasCita");
            if (!$filtro_clinica) $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            if ($fecha_inicio && $fecha_fin) {
                $stmt->bindParam(':inicio', $fecha_inicio);
                $stmt->bindParam(':fin', $fecha_fin);
            }
            $stmt->execute();
            $total_citas = $stmt->fetchColumn();

            // Veterinarios (No se ven afectados por el filtro de fecha, solo por la sucursal)
            $stmt = $db->prepare("SELECT COUNT(*) FROM Veterinarios WHERE $whereClinica");
            if (!$filtro_clinica) $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            $stmt->execute();
            $total_veterinarios = $stmt->fetchColumn();

            // Expedientes
            $stmt = $db->prepare("SELECT COUNT(*) FROM Expedientes WHERE $whereClinica $filtroFechasExp");
            if (!$filtro_clinica) $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            if ($fecha_inicio && $fecha_fin) {
                $stmt->bindParam(':inicio', $fecha_inicio);
                $stmt->bindParam(':fin', $fecha_fin);
            }
            $stmt->execute();
            $total_expedientes = $stmt->fetchColumn();

            // Mascotas únicas
            $stmt = $db->prepare("SELECT COUNT(DISTINCT ID_Mascota) FROM Expedientes WHERE $whereClinica $filtroFechasExp");
            if (!$filtro_clinica) $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            if ($fecha_inicio && $fecha_fin) {
                $stmt->bindParam(':inicio', $fecha_inicio);
                $stmt->bindParam(':fin', $fecha_fin);
            }
            $stmt->execute();
            $total_mascotas = $stmt->fetchColumn();

            // Cuidadores únicos
            $stmt = $db->prepare("SELECT COUNT(DISTINCT m.ID_Cuidador) FROM Expedientes e INNER JOIN Mascotas m ON e.ID_Mascota = m.ID_Mascota WHERE e.$whereClinica $filtroFechasExp");
            if (!$filtro_clinica) $stmt->bindParam(':id_admin', $id_admin);
            if ($filtro_clinica) $stmt->bindParam(':id_clinica', $filtro_clinica);
            if ($fecha_inicio && $fecha_fin) {
                $stmt->bindParam(':inicio', $fecha_inicio);
                $stmt->bindParam(':fin', $fecha_fin);
            }
            $stmt->execute();
            $total_cuidadores = $stmt->fetchColumn();

        } catch (PDOException $e) {
            $total_sucursales = $total_citas = $total_veterinarios = $total_expedientes = $total_mascotas = $total_cuidadores = 0;
            $titulo_dashboard = "Visión General";
            $mis_clinicas = [];
        }

        require_once '../../views/admin/dashboard.php';
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'ver') {
    $controlador = new DashboardController();
    $controlador->ver();
}
?>