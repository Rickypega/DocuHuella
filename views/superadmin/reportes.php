<?php 
require_once '../../config/auth_check.php';

// SEGURIDAD: Solo el SuperAdmin entra aquí
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Obtener el rol épico para el saludo
$nombre_rol = "Super Admin";
try {
    $stmt_rol = $db->prepare("SELECT Nombre_Rol FROM Roles WHERE ID_Rol = :id_rol");
    $stmt_rol->bindParam(':id_rol', $_SESSION['id_rol']);
    $stmt_rol->execute();
    $resultado_rol = $stmt_rol->fetchColumn();
    if ($resultado_rol) $nombre_rol = $resultado_rol;
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reportes - DocuHuella</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
    <style>

        /* Tarjetas de Reportes */
        .report-card {
            background-color: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: none;
            border-top: 5px solid var(--dh-navy);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .report-icon {
            font-size: 2.5rem;
            color: var(--dh-beige);
            margin-bottom: 15px;
        }

        .btn-pdf { background-color: #dc3545; color: white; border: none; }
        .btn-pdf:hover { background-color: #c82333; color: white; }
        
        .btn-excel { background-color: #198754; color: white; border: none; }
        .btn-excel:hover { background-color: #157347; color: white; }

        .filtro-caja {
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border-left: 4px solid var(--dh-beige);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2"><?= htmlspecialchars($nombre_rol) ?></span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/superadmin/dashboard"><i class="fas fa-chart-pie"></i> Estadísticas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/administrador.php"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/reportes.php" class="active"><i class="fas fa-file-export"></i> Gestión de Reportes</a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2" style="border-radius: 10px; padding: 12px; margin: 0 15px; border-color: rgba(255,255,255,0.2);" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; padding: 12px; margin: 0 15px 20px; width: auto !important;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">

        <div class="d-flex justify-content-end mb-2">
            <div class="user-profile text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong>Super Admin</strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>
        
        <div class="mb-4 pb-2 border-bottom">
            <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Centro de Reportes</h2>
            <p class="text-muted mt-1">Exportación de datos y auditoría del sistema</p>
        </div>

        <div class="filtro-caja mb-4">
            <form id="formFiltros" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="Todos">Todos los registros</option>
                        <option value="Activo">Solo Activos</option>
                        <option value="Inactivo">Solo Suspendidos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn w-100 fw-bold" style="background-color: var(--dh-navy); color: white;" onclick="aplicarFiltros()">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            
            <div class="col-md-4">
                <div class="report-card text-center">
                    <i class="fas fa-hospital-user report-icon"></i>
                    <h5 class="fw-bold" style="color: var(--dh-navy);">Administradores de Clínicas</h5>
                    <p class="text-muted small mb-4">Listado completo de administradores registrados, RNC y estado de sus veterinarias.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('clinicas', 'pdf')"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold" onclick="generarReporte('clinicas', 'excel')"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="report-card text-center" style="border-top-color: #0d6efd;">
                    <i class="fas fa-user-md report-icon" style="color: #0d6efd;"></i>
                    <h5 class="fw-bold" style="color: var(--dh-navy);">Personal Veterinario</h5>
                    <p class="text-muted small mb-4">Directorio de todos los médicos veterinarios, especialidades y clínicas asignadas.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('veterinarios', 'pdf')"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold" onclick="generarReporte('veterinarios', 'excel')"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="report-card text-center" style="border-top-color: #ffc107;">
                    <i class="fas fa-users report-icon" style="color: #ffc107;"></i>
                    <h5 class="fw-bold" style="color: var(--dh-navy);">Cuidadores</h5>
                    <p class="text-muted small mb-4">Listado de dueños de mascotas registrados en la plataforma.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('cuidadores', 'pdf')"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold" onclick="generarReporte('cuidadores', 'excel')"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="report-card text-center" style="border-top-color: #198754;">
                    <i class="fas fa-paw report-icon" style="color: #198754;"></i>
                    <h5 class="fw-bold" style="color: var(--dh-navy);">Población de Mascotas</h5>
                    <p class="text-muted small mb-4">Reporte demográfico de especies, razas y expedientes activos en DocuHuella.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('mascotas', 'pdf')"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold" onclick="generarReporte('mascotas', 'excel')"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        function aplicarFiltros() {
            Swal.fire({
                icon: 'success',
                title: 'Filtros Aplicados',
                text: 'Los próximos reportes que descargues usarán estos parámetros.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function generarReporte(tipo, formato) {
            let f_inicio = document.getElementById('fecha_inicio').value;
            let f_fin = document.getElementById('fecha_fin').value;
            let estado = document.getElementById('estado').value;

            let url = `../../controllers/superadmin/ReportesController.php?action=generar&tipo=${tipo}&formato=${formato}&inicio=${f_inicio}&fin=${f_fin}&estado=${estado}`;

            Swal.fire({
                title: 'Generando Documento',
                html: 'Preparando tu archivo ' + formato.toUpperCase() + '...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                }
            }).then((result) => {
           
                window.open(url, '_blank');
            });
        }
    </script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>
</html>