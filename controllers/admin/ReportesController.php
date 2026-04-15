<?php
session_start();
require_once '../../config/db.php';
require_once '../../libs/fpdf.php';

// ====================================================================
//  CLASE EXTENDIDA DE FPDF (idéntica al superadmin)
// ====================================================================
class PDF_Reporte extends FPDF {
    public $tableHeaders   = [];
    public $colWidths      = [];
    public $showTableHeaders = false;

    function Header() {
        if ($this->showTableHeaders && !empty($this->tableHeaders)) {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(197, 170, 127);
            $this->SetTextColor(0, 0, 0);
            for ($i = 0; $i < count($this->tableHeaders); $i++) {
                $this->Cell($this->colWidths[$i], 10, utf8_decode($this->tableHeaders[$i]), 1, 0, 'C', true);
            }
            $this->Ln();
        }
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}
// ====================================================================

class ReportesController {

    private $db;
    private $id_admin;

    public function __construct() {
        $this->verificarSeguridad();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->id_admin = $_SESSION['id_perfil'];
        date_default_timezone_set('America/Santo_Domingo');
    }

    private function verificarSeguridad() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            die("Acceso Denegado. Solo el Administrador puede generar estos reportes.");
        }
    }

    public function generar() {
        $tipo      = isset($_GET['tipo'])       ? $_GET['tipo']       : '';
        $formato   = isset($_GET['formato'])    ? $_GET['formato']    : '';
        $estado    = isset($_GET['estado'])     ? $_GET['estado']     : 'Todos';
        $inicio    = isset($_GET['inicio'])     && !empty($_GET['inicio']) ? $_GET['inicio'] : null;
        $fin       = isset($_GET['fin'])        && !empty($_GET['fin'])    ? $_GET['fin']    : null;
        // Filtro de sucursal — si viene, verificar que pertenece al admin
        $id_clinica = isset($_GET['id_clinica']) && !empty($_GET['id_clinica']) ? (int)$_GET['id_clinica'] : null;

        // Verificar que la clínica filtrada pertenece a este admin (seguridad)
        if ($id_clinica) {
            $check = $this->db->prepare("SELECT ID_Clinica FROM clinicas WHERE ID_Clinica = :id AND ID_Admin = :adm");
            $check->bindParam(':id',  $id_clinica);
            $check->bindParam(':adm', $this->id_admin);
            $check->execute();
            if (!$check->fetch()) {
                die("Acceso denegado a esa sucursal.");
            }
        }

        // Cláusula WHERE base para clinicas del admin
        // Si hay filtro de sucursal: solo esa
        // Si no: todas las del admin
        if ($id_clinica) {
            $whereClinica_exp = "e.ID_Clinica = :id_clinica";
            $whereClinica_vet = "v.ID_Clinica = :id_clinica";
            $whereClinica_cli = "c.ID_Clinica = :id_clinica";
        } else {
            $whereClinica_exp = "e.ID_Clinica IN (SELECT ID_Clinica FROM clinicas WHERE ID_Admin = :id_admin)";
            $whereClinica_vet = "v.ID_Clinica IN (SELECT ID_Clinica FROM clinicas WHERE ID_Admin = :id_admin)";
            $whereClinica_cli = "c.ID_Admin = :id_admin";
        }

        $datos  = [];
        $titulo = "";
        $resumenEstructurado = [];

        switch ($tipo) {

            // ── SUCURSALES ───────────────────────────────────────────────
            case 'sucursales':
                $titulo = "Reporte de Sucursales";
                $query  = "SELECT c.Nombre_Sucursal AS Sucursal, c.Direccion, c.Telefono, c.RNC, c.Estado,
                                   DATE(c.Fecha_Registro) AS Fecha_Registro
                            FROM clinicas c
                            WHERE c.ID_Admin = :id_admin";
                if ($id_clinica) {
                    $query .= " AND c.ID_Clinica = :id_clinica";
                }
                if ($estado !== 'Todos') {
                    $query .= " AND c.Estado = :estado";
                }
                $query .= " ORDER BY c.Nombre_Sucursal ASC";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_admin', $this->id_admin);
                if ($id_clinica)        $stmt->bindParam(':id_clinica', $id_clinica);
                if ($estado !== 'Todos') $stmt->bindParam(':estado',    $estado);
                $stmt->execute();
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            // ── VETERINARIOS ─────────────────────────────────────────────
            case 'veterinarios':
                $titulo = "Reporte de Personal Veterinario";
                $query  = "SELECT v.Nombre, v.Apellido, esp.Nombre_Especialidad AS Especialidad,
                                   cl.Nombre_Sucursal AS Sucursal, v.Telefono, u.Correo, u.Estado,
                                   DATE(u.Fecha_Registro) AS Fecha_Registro
                            FROM veterinarios v
                            INNER JOIN usuarios   u   ON v.ID_Usuario      = u.ID_Usuario
                            INNER JOIN clinicas   cl  ON v.ID_Clinica      = cl.ID_Clinica
                            LEFT  JOIN especialidades esp ON v.ID_Especialidad = esp.ID_Especialidad
                            WHERE $whereClinica_vet";
                if ($estado !== 'Todos') $query .= " AND u.Estado = :estado";
                if ($inicio)             $query .= " AND DATE(u.Fecha_Registro) >= :inicio";
                if ($fin)                $query .= " AND DATE(u.Fecha_Registro) <= :fin";
                $query .= " ORDER BY u.Fecha_Registro DESC";

                $stmt = $this->db->prepare($query);
                if ($id_clinica) $stmt->bindParam(':id_clinica', $id_clinica);
                else             $stmt->bindParam(':id_admin',   $this->id_admin);
                if ($estado !== 'Todos') $stmt->bindParam(':estado', $estado);
                if ($inicio) $stmt->bindParam(':inicio', $inicio);
                if ($fin)    $stmt->bindParam(':fin',    $fin);
                $stmt->execute();
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            // ── MASCOTAS ─────────────────────────────────────────────────
            case 'mascotas':
                $titulo = "Reporte Poblacional de Mascotas";
                $query  = "SELECT m.Nombre AS Mascota, esp.Nombre_Especie AS Especie,
                                   r.Nombre_Raza AS Raza, m.Sexo, co.Nombre_Color AS Color,
                                   cu.Nombre AS Dueno, u.Estado,
                                   DATE(e.Fecha_Apertura) AS Fecha_Registro
                            FROM expedientes e
                            INNER JOIN mascotas  m   ON e.ID_Mascota  = m.ID_Mascota
                            INNER JOIN cuidadores cu  ON m.ID_Cuidador = cu.ID_Cuidador
                            INNER JOIN usuarios   u   ON cu.ID_Usuario = u.ID_Usuario
                            LEFT  JOIN especies   esp ON m.ID_Especie  = esp.ID_Especie
                            LEFT  JOIN razas      r   ON m.ID_Raza     = r.ID_Raza
                            LEFT  JOIN colores    co  ON m.ID_Color    = co.ID_Color
                            WHERE $whereClinica_exp";
                if ($inicio) $query .= " AND DATE(e.Fecha_Apertura) >= :inicio";
                if ($fin)    $query .= " AND DATE(e.Fecha_Apertura) <= :fin";
                $query .= " ORDER BY m.Nombre ASC";

                $stmt = $this->db->prepare($query);
                if ($id_clinica) $stmt->bindParam(':id_clinica', $id_clinica);
                else             $stmt->bindParam(':id_admin',   $this->id_admin);
                if ($inicio) $stmt->bindParam(':inicio', $inicio);
                if ($fin)    $stmt->bindParam(':fin',    $fin);
                $stmt->execute();
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Resumen estructurado por especie (igual que superadmin)
                foreach ($datos as $row) {
                    $esp  = $row['Especie'] ?? 'Desconocida';
                    $sex  = $row['Sexo']    ?? 'N/A';
                    $raza = $row['Raza']    ?? 'Mestizo';
                    if (!isset($resumenEstructurado[$esp])) {
                        $resumenEstructurado[$esp] = ['M' => 0, 'F' => 0, 'total' => 0, 'razas' => []];
                    }
                    $resumenEstructurado[$esp]['total']++;
                    if ($sex === 'M') $resumenEstructurado[$esp]['M']++;
                    elseif ($sex === 'F') $resumenEstructurado[$esp]['F']++;
                    if (!isset($resumenEstructurado[$esp]['razas'][$raza])) {
                        $resumenEstructurado[$esp]['razas'][$raza] = 0;
                    }
                    $resumenEstructurado[$esp]['razas'][$raza]++;
                }
                break;

            // ── CUIDADORES ───────────────────────────────────────────────
            case 'cuidadores':
                $titulo = "Reporte de Cuidadores y Clientes";
                // Cuidadores que tengan mascotas con expedientes en mis clínicas
                $query  = "SELECT DISTINCT cu.Nombre, cu.Apellido, cu.Cedula, cu.Telefono,
                                   u.Correo, u.Estado, DATE(u.Fecha_Registro) AS Fecha_Registro
                            FROM cuidadores cu
                            INNER JOIN usuarios   u ON cu.ID_Usuario  = u.ID_Usuario
                            INNER JOIN mascotas   m ON m.ID_Cuidador  = cu.ID_Cuidador
                            INNER JOIN expedientes e ON e.ID_Mascota  = m.ID_Mascota
                            WHERE $whereClinica_exp";
                if ($estado !== 'Todos') $query .= " AND u.Estado = :estado";
                if ($inicio)             $query .= " AND DATE(u.Fecha_Registro) >= :inicio";
                if ($fin)                $query .= " AND DATE(u.Fecha_Registro) <= :fin";
                $query .= " ORDER BY u.Fecha_Registro DESC";

                $stmt = $this->db->prepare($query);
                if ($id_clinica) $stmt->bindParam(':id_clinica', $id_clinica);
                else             $stmt->bindParam(':id_admin',   $this->id_admin);
                if ($estado !== 'Todos') $stmt->bindParam(':estado', $estado);
                if ($inicio) $stmt->bindParam(':inicio', $inicio);
                if ($fin)    $stmt->bindParam(':fin',    $fin);
                $stmt->execute();
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

            default:
                die("Tipo de reporte no válido.");
        }

        // EXPORTAR
        if ($formato === 'excel') {
            $this->exportarExcel($titulo, $datos, $resumenEstructurado);
        } elseif ($formato === 'pdf') {
            $this->exportarPDF($titulo, $datos, $resumenEstructurado);
        } else {
            die("Formato no soportado.");
        }
    }

    // ── EXCEL ───────────────────────────────────────────────────────────────
    private function exportarExcel($titulo, $datos, $resumen) {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . str_replace(' ', '_', $titulo) . "_" . date('Ymd_Hi') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF";
        echo "<table style='font-family:Calibri,sans-serif;border-collapse:collapse;'>";
        echo "<tr><td style='height:15px;border:none;'></td></tr>";

        if (count($datos) > 0) {
            $columnas = count($datos[0]) + 1;
            $fecha    = date('d/m/Y h:i A');
            $total    = count($datos);

            $espacioIzquierda = floor(($columnas - 4) / 2);
            $espacioDerecha   = ceil(($columnas - 4) / 2);

            if (!empty($resumen)) {
                echo "<tr><th colspan='$columnas' style='background-color:#1A2D40;color:white;font-size:16px;text-align:center;padding:5px;font-weight:bold;'>Resumen Poblacional de Mascotas</th></tr>";
                echo "<tr><th colspan='$columnas' style='color:#000;font-size:11px;text-align:center;font-weight:bold;'>Total: $total &nbsp;|&nbsp; Fecha: $fecha</th></tr>";
                echo "<tr><td colspan='$columnas' style='height:20px;border:none;'></td></tr>";

                foreach ($resumen as $esp => $data) {
                    $totalRazas = count($data['razas']);
                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                    echo "<td colspan='2' style='border-top:2px solid #000;border-left:2px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;font-weight:bold;padding-left:5px;'>Especie: $esp</td>";
                    echo "<td colspan='2' style='border-top:2px solid #000;border-right:2px solid #000;border-bottom:1px solid #000;padding-left:5px;'>Sexo (M: {$data['M']} - F: {$data['F']})</td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                    echo "<td colspan='2' style='border-left:2px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;font-weight:bold;padding-left:5px;'>Razas:</td>";
                    echo "<td colspan='2' style='border-right:2px solid #000;border-bottom:1px solid #000;'></td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";

                    $cnt = 1;
                    foreach ($data['razas'] as $raza => $cant) {
                        echo "<tr>";
                        echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                        echo "<td colspan='2' style='border-left:2px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;font-weight:bold;padding-left:5px;'>#$cnt</td>";
                        echo "<td colspan='2' style='border-right:2px solid #000;border-bottom:1px solid #000;padding-left:5px;'>$raza = $cant</td>";
                        echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                        echo "</tr>";
                        $cnt++;
                    }

                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                    echo "<td colspan='2' style='border-left:2px solid #000;border-bottom:2px solid #000;border-right:1px solid #000;font-weight:bold;padding-left:5px;'>Total $esp: {$data['total']}</td>";
                    echo "<td colspan='2' style='border-right:2px solid #000;border-bottom:2px solid #000;font-weight:bold;padding-left:5px;'>Total Razas: $totalRazas</td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";
                    echo "<tr><td colspan='$columnas' style='height:20px;border:none;'></td></tr>";
                }
            } else {
                echo "<tr><th colspan='$columnas' style='color:#000;font-size:11px;text-align:center;font-weight:bold;'>Total: $total &nbsp;|&nbsp; Fecha: $fecha</th></tr>";
                echo "<tr><td colspan='$columnas' style='height:20px;border:none;'></td></tr>";
            }

            echo "<tr><th colspan='$columnas' style='background-color:#1A2D40;color:white;font-size:16px;text-align:center;padding:5px;font-weight:bold;'>$titulo</th></tr>";

            echo "<tr>";
            echo "<th style='background-color:#c5aa7f;color:#000;font-weight:bold;border:2px solid #000;text-align:center;padding:5px 10px;width:40px;'>#</th>";
            foreach (array_keys($datos[0]) as $col) {
                echo "<th style='background-color:#c5aa7f;color:#000;font-weight:bold;border:2px solid #000;text-align:center;padding:5px 15px;min-width:120px;'>" . strtoupper(str_replace('_', ' ', $col)) . "</th>";
            }
            echo "</tr>";

            $n = 1;
            foreach ($datos as $fila) {
                echo "<tr>";
                echo "<td style='text-align:center;font-weight:bold;border:1px solid #000;padding:5px;'>$n</td>";
                foreach ($fila as $val) {
                    echo "<td style='border:1px solid #000;padding:5px 15px;white-space:nowrap;'>" . htmlspecialchars((string)$val) . "</td>";
                }
                echo "</tr>";
                $n++;
            }
        } else {
            echo "<tr><th style='background-color:#1A2D40;color:white;font-size:18px;text-align:center;'>$titulo</th></tr>";
            echo "<tr><td style='text-align:center;padding:20px;'><b>No se encontraron registros para los filtros seleccionados.</b></td></tr>";
        }

        echo "</table>";
        exit();
    }

    // ── PDF ─────────────────────────────────────────────────────────────────
    private function exportarPDF($titulo, $datos, $resumen) {
        $pdf = new PDF_Reporte();
        $pdf->AliasNbPages();
        $pdf->AddPage('L');
        $pdf->Ln(5);

        if (count($datos) > 0) {
            $fecha = date('d/m/Y h:i A');
            $total = count($datos);

            if (!empty($resumen)) {
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->SetFillColor(26, 45, 64);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(0, 10, utf8_decode('Resumen Poblacional de Mascotas'), 0, 1, 'C', true);
            }

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(0, 6, utf8_decode("Total de registros: $total   |   Fecha: $fecha"), 0, 1, 'C');
            $pdf->Ln(5);

            if (!empty($resumen)) {
                $anchoCol1    = 50;
                $anchoCol2    = 70;
                $margenCentro = (277 - ($anchoCol1 + $anchoCol2)) / 2;

                foreach ($resumen as $esp => $data) {
                    $totalRazas = count($data['razas']);
                    $pdf->SetX(10 + $margenCentro);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell($anchoCol1, 6, utf8_decode("Especie: $esp"), 'TLR', 0, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell($anchoCol2, 6, utf8_decode("Sexo (M: {$data['M']} - F: {$data['F']})"), 'TR', 1, 'L');

                    $pdf->SetX(10 + $margenCentro);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell($anchoCol1, 6, utf8_decode("Razas:"), 'LR', 0, 'L');
                    $pdf->Cell($anchoCol2, 6, "", 'R', 1, 'L');

                    $pdf->SetFont('Arial', '', 9);
                    $cntRaza = 1;
                    foreach ($data['razas'] as $raza => $cant) {
                        $pdf->SetX(10 + $margenCentro);
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->Cell($anchoCol1, 6, "#$cntRaza", 'LR', 0, 'L');
                        $pdf->SetFont('Arial', '', 9);
                        $pdf->Cell($anchoCol2, 6, utf8_decode("$raza = $cant"), 'R', 1, 'L');
                        $cntRaza++;
                    }

                    $pdf->SetX(10 + $margenCentro);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell($anchoCol1, 6, utf8_decode("Total $esp: {$data['total']}"), 'LBR', 0, 'L');
                    $pdf->Cell($anchoCol2, 6, utf8_decode("Total Razas: $totalRazas"), 'BR', 1, 'L');
                    $pdf->Ln(6);
                }
            }

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetFillColor(26, 45, 64);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C', true);
            $pdf->Ln(2);

            $anchoNum       = 12;
            $colsRestantes  = count($datos[0]);
            $anchoCol       = (277 - $anchoNum) / $colsRestantes;

            $tituloCols  = ['#'];
            $anchosCols  = [$anchoNum];
            foreach (array_keys($datos[0]) as $col) {
                $tituloCols[] = strtoupper(str_replace('_', ' ', $col));
                $anchosCols[] = $anchoCol;
            }

            $pdf->tableHeaders     = $tituloCols;
            $pdf->colWidths        = $anchosCols;
            $pdf->showTableHeaders = true;

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(197, 170, 127);
            $pdf->SetTextColor(0, 0, 0);
            for ($i = 0; $i < count($tituloCols); $i++) {
                $pdf->Cell($anchosCols[$i], 10, utf8_decode($tituloCols[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 9);
            $n = 1;
            foreach ($datos as $fila) {
                $pdf->Cell($anchoNum, 10, $n, 1, 0, 'C');
                foreach ($fila as $val) {
                    $pdf->Cell($anchoCol, 10, utf8_decode((string)$val), 1, 0, 'C');
                }
                $pdf->Ln();
                $n++;
            }
        } else {
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'No se encontraron registros para los filtros seleccionados.', 0, 1, 'C');
        }

        $pdf->Output('D', str_replace(' ', '_', $titulo) . '_' . date('Ymd_Hi') . '.pdf');
        exit();
    }
}

// Ruteo
if (isset($_GET['action']) && $_GET['action'] === 'generar') {
    $ctrl = new ReportesController();
    $ctrl->generar();
} else {
    header("Location: ../../views/admin/reportes.php");
    exit();
}
