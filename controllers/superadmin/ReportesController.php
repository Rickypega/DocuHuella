<?php
session_start();
require_once '../../config/db.php';
require_once '../../libs/fpdf.php'; 

// ====================================================================
//  CLASE EXTENDIDA DE FPDF PARA CABECERAS Y PIE DE PÁGINA 
// ====================================================================
class PDF_Reporte extends FPDF {
    public $tableHeaders = [];
    public $colWidths = [];
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

    public function __construct() {
        $this->verificarSeguridad();
        $database = new Database();
        $this->db = $database->getConnection();
        date_default_timezone_set('America/Santo_Domingo');
    }

    private function verificarSeguridad() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
            die("Acceso Denegado. Solo el SuperAdmin puede generar reportes.");
        }
    }

    public function generar() {
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
        $formato = isset($_GET['formato']) ? $_GET['formato'] : '';
        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'Todos';
        
        //  CAPTURAMOS LAS FECHAS DEL FILTRO
        $inicio = isset($_GET['inicio']) && !empty($_GET['inicio']) ? $_GET['inicio'] : null;
        $fin = isset($_GET['fin']) && !empty($_GET['fin']) ? $_GET['fin'] : null;
        
        $datos = [];
        $tituloReporte = "";
        $resumenEstructurado = []; 

        // 1. OBTENER LOS DATOS BASE
        switch ($tipo) {
            case 'clinicas':
                $tituloReporte = "Reporte de Clinicas y Franquicias";
                $query = "SELECT a.Nombre, a.Apellido, a.Telefono, a.Cedula, u.Correo, u.Estado, DATE(u.Fecha_Registro) AS Fecha_Registro, c.Nombre_Sucursal, c.RNC 
                          FROM administrador a
                          INNER JOIN usuarios u ON a.ID_Usuario = u.ID_Usuario
                          LEFT JOIN clinicas c ON a.ID_Admin = c.ID_Admin WHERE 1=1";
                break;

            case 'veterinarios':
                $tituloReporte = "Reporte de Personal Veterinario";
                $query = "SELECT v.Nombre, v.Apellido, e.Nombre_Especialidad AS Especialidad, v.Telefono, u.Correo, u.Estado, DATE(u.Fecha_Registro) AS Fecha_Registro 
                          FROM veterinarios v
                          INNER JOIN usuarios u ON v.ID_Usuario = u.ID_Usuario
                          LEFT JOIN especialidades e ON v.ID_Especialidad = e.ID_Especialidad WHERE 1=1";
                break;

            case 'cuidadores':
                $tituloReporte = "Reporte de Cuidadores y Clientes";
                $query = "SELECT c.Nombre, c.Apellido, c.Cedula, c.Telefono, u.Correo, u.Estado, DATE(u.Fecha_Registro) AS Fecha_Registro 
                          FROM cuidadores c
                          INNER JOIN usuarios u ON c.ID_Usuario = u.ID_Usuario WHERE 1=1";
                break;

            case 'mascotas':
                $tituloReporte = "Reporte Poblacional de Mascotas";
                $query = "SELECT m.Nombre AS Mascota, e.Nombre_Especie AS Especie, r.Nombre_Raza AS Raza, m.Sexo, co.Nombre_Color AS Color, c.Nombre AS Dueno, u.Estado, DATE(u.Fecha_Registro) AS Fecha_Registro 
                          FROM mascotas m
                          INNER JOIN cuidadores c ON m.ID_Cuidador = c.ID_Cuidador
                          INNER JOIN usuarios u ON c.ID_Usuario = u.ID_Usuario
                          LEFT JOIN especies e ON m.ID_Especie = e.ID_Especie
                          LEFT JOIN razas r ON m.ID_Raza = r.ID_Raza
                          LEFT JOIN colores co ON m.ID_Color = co.ID_Color WHERE 1=1";
                break;

            default:
                die("Tipo de reporte no válido.");
        }

        // 2. APLICAR FILTROS DINÁMICAMENTE A LA CONSULTA
        if ($estado != 'Todos') {
            $query .= " AND u.Estado = :estado";
        }
        if ($inicio) {
            $query .= " AND DATE(u.Fecha_Registro) >= :inicio";
        }
        if ($fin) {
            $query .= " AND DATE(u.Fecha_Registro) <= :fin";
        }

        // 3. ORDENAR (Mascotas por nombre, los demás por fecha de registro descendente)
        if ($tipo == 'mascotas') {
            $query .= " ORDER BY m.Nombre ASC";
        } else {
            $query .= " ORDER BY u.Fecha_Registro DESC";
        }

        // 4. PREPARAR Y EJECUTAR
        $stmt = $this->db->prepare($query);
        
        // Asignar variables a los parámetros si existen
        if ($estado != 'Todos') $stmt->bindParam(':estado', $estado);
        if ($inicio) $stmt->bindParam(':inicio', $inicio);
        if ($fin) $stmt->bindParam(':fin', $fin);
        
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- LÓGICA DEL RESUMEN ESTRUCTURADO (Para Mascotas) ---
        if ($tipo == 'mascotas' && count($datos) > 0) {
            foreach ($datos as $row) {
                $esp = $row['Especie'] ?? 'Desconocida';
                $sex = $row['Sexo'] ?? 'N/A';
                $raza = $row['Raza'] ?? 'Mestizo';
                
                if (!isset($resumenEstructurado[$esp])) {
                    $resumenEstructurado[$esp] = ['M' => 0, 'F' => 0, 'total' => 0, 'razas' => []];
                }
                
                $resumenEstructurado[$esp]['total']++;
                if ($sex == 'M') $resumenEstructurado[$esp]['M']++;
                elseif ($sex == 'F') $resumenEstructurado[$esp]['F']++;
                
                if (!isset($resumenEstructurado[$esp]['razas'][$raza])) {
                    $resumenEstructurado[$esp]['razas'][$raza] = 0;
                }
                $resumenEstructurado[$esp]['razas'][$raza]++;
            }
        }

        // 5. EXPORTAR AL FORMATO SELECCIONADO
        if ($formato == 'excel') {
            $this->exportarExcel($tituloReporte, $datos, $resumenEstructurado);
        } elseif ($formato == 'pdf') {
            $this->exportarPDF($tituloReporte, $datos, $resumenEstructurado);
        } else {
            die("Formato no soportado.");
        }
    }

    // --- MÉTODOS DE EXPORTACIÓN ---

    private function exportarExcel($titulo, $datos, $resumen) {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . str_replace(' ', '_', $titulo) . "_" . date('Ymd_Hi') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF"; 
        echo "<table style='font-family: Calibri, sans-serif; border-collapse: collapse;'>";
        echo "<tr><td style='height:15px; border:none;'></td></tr>"; 

        if (count($datos) > 0) {
            $columnas = count($datos[0]) + 1; 
            $fecha = date('d/m/Y h:i A');
            $total = count($datos);

            $espacioIzquierda = floor(($columnas - 4) / 2);
            $espacioDerecha = ceil(($columnas - 4) / 2);

            if (!empty($resumen)) {
                echo "<tr><th colspan='$columnas' style='background-color:#1A2D40; color:white; font-size:16px; text-align:center; padding:5px; font-weight:bold;'>Resumen Poblacional de Mascotas</th></tr>";
                echo "<tr><th colspan='$columnas' style='color:#000; font-size:11px; text-align:center; font-weight:bold;'>Total de registros: $total &nbsp;&nbsp;|&nbsp;&nbsp; Fecha: $fecha</th></tr>";
                echo "<tr><td colspan='$columnas' style='height:20px; border:none;'></td></tr>"; 
                
                foreach ($resumen as $esp => $data) {
                    $totalRazas = count($data['razas']);
                    
                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>"; 
                    echo "<td colspan='2' style='border-top: 2px solid #000; border-left: 2px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold; padding-left:5px;'>Especie: $esp</td>";
                    echo "<td colspan='2' style='border-top: 2px solid #000; border-right: 2px solid #000; border-bottom: 1px solid #000; padding-left:5px;'>Sexo (M: {$data['M']} - F: {$data['F']})</td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                    echo "<td colspan='2' style='border-left: 2px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold; padding-left:5px;'>Razas:</td>";
                    echo "<td colspan='2' style='border-right: 2px solid #000; border-bottom: 1px solid #000;'></td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";

                    $contadorRaza = 1;
                    foreach ($data['razas'] as $raza => $cant) {
                        echo "<tr>";
                        echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                        echo "<td colspan='2' style='border-left: 2px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold; padding-left:5px;'>#$contadorRaza</td>";
                        echo "<td colspan='2' style='border-right: 2px solid #000; border-bottom: 1px solid #000; padding-left:5px;'>$raza = $cant</td>";
                        echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                        echo "</tr>";
                        $contadorRaza++;
                    }

                    echo "<tr>";
                    echo "<td colspan='$espacioIzquierda' style='border:none;'></td>";
                    echo "<td colspan='2' style='border-left: 2px solid #000; border-bottom: 2px solid #000; border-right: 1px solid #000; font-weight:bold; padding-left:5px;'>Total $esp: {$data['total']}</td>";
                    echo "<td colspan='2' style='border-right: 2px solid #000; border-bottom: 2px solid #000; font-weight:bold; padding-left:5px;'>Total Razas: $totalRazas</td>";
                    echo "<td colspan='$espacioDerecha' style='border:none;'></td>";
                    echo "</tr>";
                    
                    echo "<tr><td colspan='$columnas' style='height:20px; border:none;'></td></tr>"; 
                }
            } else {
                echo "<tr><th colspan='$columnas' style='color:#000; font-size:11px; text-align:center; font-weight:bold;'>Total de registros: $total &nbsp;&nbsp;|&nbsp;&nbsp; Fecha: $fecha</th></tr>";
                echo "<tr><td colspan='$columnas' style='height:20px; border:none;'></td></tr>"; 
            }

            echo "<tr><th colspan='$columnas' style='background-color:#1A2D40; color:white; font-size:16px; text-align:center; padding:5px; font-weight:bold;'>$titulo</th></tr>";

            echo "<tr>";
            echo "<th style='background-color:#c5aa7f; color:#000; font-weight:bold; border: 2px solid #000; text-align:center; padding: 5px 10px; width:40px;'>#</th>";
            foreach (array_keys($datos[0]) as $columna) {
                echo "<th style='background-color:#c5aa7f; color:#000; font-weight:bold; border: 2px solid #000; text-align:center; padding: 5px 15px; min-width: 120px;'>" . strtoupper(str_replace('_', ' ', $columna)) . "</th>";
            }
            echo "</tr>";

            $contador = 1;
            foreach ($datos as $fila) {
                echo "<tr>";
                echo "<td style='text-align:center; font-weight:bold; border: 1px solid #000; padding: 5px;'>" . $contador . "</td>";
                foreach ($fila as $valor) {
                    echo "<td style='border: 1px solid #000; padding: 5px 15px; white-space: nowrap;'>" . htmlspecialchars((string)$valor) . "</td>";
                }
                echo "</tr>";
                $contador++;
            }
        } else {
            echo "<tr><th style='background-color:#1A2D40; color:white; font-size:18px; text-align:center;'>$titulo</th></tr>";
            echo "<tr><td style='text-align:center; padding:20px;'><b>No se encontraron registros para los filtros seleccionados.</b></td></tr>";
        }
        echo "</table>";
        exit();
    }

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
                $anchoCol1 = 50; 
                $anchoCol2 = 70; 
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
                    $contadorRaza = 1;
                    foreach ($data['razas'] as $raza => $cant) {
                        $pdf->SetX(10 + $margenCentro);
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->Cell($anchoCol1, 6, "#$contadorRaza", 'LR', 0, 'L');
                        $pdf->SetFont('Arial', '', 9);
                        $pdf->Cell($anchoCol2, 6, utf8_decode("$raza = $cant"), 'R', 1, 'L');
                        $contadorRaza++;
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

            $anchoNum = 12; 
            $columnasRestantes = count($datos[0]);
            $anchoColumna = (277 - $anchoNum) / $columnasRestantes;

            $titulosColumnas = ['#'];
            $anchosColumnas = [$anchoNum];
            foreach (array_keys($datos[0]) as $columna) {
                $titulosColumnas[] = strtoupper(str_replace('_', ' ', $columna));
                $anchosColumnas[] = $anchoColumna;
            }

            $pdf->tableHeaders = $titulosColumnas;
            $pdf->colWidths = $anchosColumnas;
            $pdf->showTableHeaders = true;

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(197, 170, 127); 
            $pdf->SetTextColor(0, 0, 0); 
            for ($i = 0; $i < count($titulosColumnas); $i++) {
                $pdf->Cell($anchosColumnas[$i], 10, utf8_decode($titulosColumnas[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 9);
            $contador = 1;
            foreach ($datos as $fila) {
                $pdf->Cell($anchoNum, 10, $contador, 1, 0, 'C');
                foreach ($fila as $valor) {
                    $pdf->Cell($anchoColumna, 10, utf8_decode((string)$valor), 1, 0, 'C');
                }
                $pdf->Ln();
                $contador++;
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

if (isset($_GET['action']) && $_GET['action'] == 'generar') {
    $controlador = new ReportesController();
    $controlador->generar();
} else {
    header("Location: ../../views/superadmin/reportes.php");
    exit();
}
