<?php
require_once '../../config/auth_check.php';

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Obtener mis sucursales para el filtro
$stmt_cli = $db->prepare("SELECT ID_Clinica, Nombre_Sucursal FROM clinicas WHERE ID_Admin = :id ORDER BY Nombre_Sucursal ASC");
$stmt_cli->bindParam(':id', $_SESSION['id_perfil']);
$stmt_cli->execute();
$mis_clinicas = $stmt_cli->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        /* Tarjetas de Reportes */
        .report-card {
            background-color: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: none;
            border-top: 5px solid var(--dh-navy);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .report-icon {
            font-size: 2.5rem;
            color: var(--dh-beige);
            margin-bottom: 15px;
        }

        .btn-pdf {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-pdf:hover {
            background-color: #c82333;
            color: white;
        }

        .btn-excel {
            background-color: #198754;
            color: white;
            border: none;
        }

        .btn-excel:hover {
            background-color: #157347;
            color: white;
        }

        .filtro-caja {
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border-left: 4px solid var(--dh-beige);
        }
    </style>
</head>

<body>

    <!-- Encabezado Móvil -->
    <div class="mobile-header d-md-none p-3 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold text-white"><i class="fas fa-paw" style="color:var(--dh-beige);"></i> DocuHuella</h4>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color:var(--dh-beige);"></i> DocuHuella
            </h3>
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>
        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/admin/dashboard"><i class="fas fa-chart-pie"></i> Mi Resumen</a>
            <a href="<?= URL_BASE ?>/views/admin/clinicas.php"><i class="fas fa-hospital"></i> Mis Sucursales</a>
            <a href="<?= URL_BASE ?>/views/admin/registrar_vet.php"><i class="fas fa-user-md"></i> Veterinarios</a>
            <a href="<?= URL_BASE ?>/views/admin/reportes.php" class="active"><i class="fas fa-file-medical-alt"></i>
                Reportes Clínicos</a>
            <a href="#" id="enlace-mis-notas" onclick="mostrarPanelNotas(); marcarActivoSidebar(this); return false;">
                <i class="fas fa-sticky-note"></i> Mis Notas
            </a>
        </nav>
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2"
                style="border-radius:10px;padding:12px;margin:0 15px;border-color:rgba(255,255,255,0.2);"
                data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i><span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout"
                class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2"
                style="border-radius:10px;padding:12px;margin:0 15px 20px;width:auto !important;">
                <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">

        <!-- Envoltorio para ocultar al mostrar notas -->
        <div id="contenido-dashboard">

        <!-- Bienvenida -->
        <div class="d-flex justify-content-end mb-2">
            <div class="text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <!-- Encabezado -->
        <div class="mb-4 pb-2 border-bottom">
            <h2 class="fw-bold mb-0" style="color:var(--dh-navy);">Centro de Reportes</h2>
            <p class="text-muted mt-1">Exportación de datos de tus sucursales y personal</p>
        </div>

        <!-- Filtros -->
        <div class="filtro-caja mb-4">
            <form id="formFiltros" class="row align-items-end g-3">

                <!-- Sucursal -->
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Sucursal</label>
                    <select class="form-select" id="filtro_clinica" name="id_clinica">
                        <option value="">Todas mis sucursales</option>
                        <?php foreach ($mis_clinicas as $c): ?>
                            <option value="<?= $c['ID_Clinica'] ?>"><?= htmlspecialchars($c['Nombre_Sucursal']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha Inicio -->
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted small">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                </div>

                <!-- Fecha Fin -->
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted small">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                </div>

                <!-- Estado -->
                <div class="col-md-2">
                    <label class="form-label fw-bold text-muted small">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="Todos">Todos los registros</option>
                        <option value="Activo">Solo Activos</option>
                        <option value="Inactivo">Solo Suspendidos</option>
                    </select>
                </div>

                <!-- Botón aplicar -->
                <div class="col-md-3">
                    <button type="button" class="btn w-100 fw-bold" style="background-color:var(--dh-navy);color:white;"
                        onclick="aplicarFiltros()">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                </div>

            </form>
        </div>

        <!-- Tarjetas de Reportes -->
        <div class="row g-4">

            <!-- Mis Sucursales -->
            <div class="col-md-3">
                <div class="report-card text-center">
                    <i class="fas fa-hospital report-icon"></i>
                    <h5 class="fw-bold" style="color:var(--dh-navy);">Mis Sucursales</h5>
                    <p class="text-muted small mb-4">Listado de tus clínicas registradas con RNC, dirección, teléfono y
                        estado.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('sucursales','pdf')"><i
                                class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold"
                            onclick="generarReporte('sucursales','excel')"><i class="fas fa-file-excel"></i>
                            Excel</button>
                    </div>
                </div>
            </div>

            <!-- Personal Veterinario -->
            <div class="col-md-3">
                <div class="report-card text-center" style="border-top-color:#0d6efd;">
                    <i class="fas fa-user-md report-icon" style="color:#0d6efd;"></i>
                    <h5 class="fw-bold" style="color:var(--dh-navy);">Personal Veterinario</h5>
                    <p class="text-muted small mb-4">Directorio de los médicos veterinarios de tus sucursales con
                        especialidades y estado.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold"
                            onclick="generarReporte('veterinarios','pdf')"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold"
                            onclick="generarReporte('veterinarios','excel')"><i class="fas fa-file-excel"></i>
                            Excel</button>
                    </div>
                </div>
            </div>

            <!-- Mascotas -->
            <div class="col-md-3">
                <div class="report-card text-center" style="border-top-color:#198754;">
                    <i class="fas fa-paw report-icon" style="color:#198754;"></i>
                    <h5 class="fw-bold" style="color:var(--dh-navy);">Población de Mascotas</h5>
                    <p class="text-muted small mb-4">Reporte demográfico de mascotas con expedientes abiertos en tus
                        sucursales.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('mascotas','pdf')"><i
                                class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold"
                            onclick="generarReporte('mascotas','excel')"><i class="fas fa-file-excel"></i>
                            Excel</button>
                    </div>
                </div>
            </div>

            <!-- Cuidadores -->
            <div class="col-md-3">
                <div class="report-card text-center" style="border-top-color:#ffc107;">
                    <i class="fas fa-users report-icon" style="color:#ffc107;"></i>
                    <h5 class="fw-bold" style="color:var(--dh-navy);">Cuidadores / Clientes</h5>
                    <p class="text-muted small mb-4">Listado de dueños de mascotas atendidas en tus clínicas.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-pdf w-50 fw-bold" onclick="generarReporte('cuidadores','pdf')"><i
                                class="fas fa-file-pdf"></i> PDF</button>
                        <button class="btn btn-sm btn-excel w-50 fw-bold"
                            onclick="generarReporte('cuidadores','excel')"><i class="fas fa-file-excel"></i>
                            Excel</button>
                    </div>
                </div>
            </div>

        </div>

        </div><!-- /#contenido-dashboard -->

        <!-- Panel de Notas -->
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>

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
            const f_inicio = document.getElementById('fecha_inicio').value;
            const f_fin = document.getElementById('fecha_fin').value;
            const estado = document.getElementById('estado').value;
            const id_clinica = document.getElementById('filtro_clinica').value;

            const url = `../../controllers/admin/ReportesController.php?action=generar&tipo=${tipo}&formato=${formato}&inicio=${f_inicio}&fin=${f_fin}&estado=${estado}&id_clinica=${id_clinica}`;

            Swal.fire({
                title: 'Generando Documento',
                html: 'Preparando tu archivo ' + formato.toUpperCase() + '...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => { Swal.showLoading(); }
            }).then(() => {
                window.open(url, '_blank');
            });
        }
    </script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
    <script>
        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>

</html>