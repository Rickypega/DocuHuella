<?php 
require_once APP_PATH . '/config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/veterinario/pacientes");
    exit();
}
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
        .profile-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: none;
        }
        .pet-header-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .info-pill {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--dh-navy);
        }
        .history-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .timeline-item {
            padding-left: 20px;
            border-left: 2px solid #eee;
            position: relative;
            padding-bottom: 25px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--dh-navy);
        }
        .btn-edit-float {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            color: var(--dh-navy);
            transition: all 0.3s;
            border: none;
            z-index: 10;
        }
        .btn-edit-float:hover {
            background: var(--dh-navy);
            color: white;
            transform: scale(1.1);
        }

        /* Efecto Visual para los Tabs del Expediente */
        #medTab .nav-link {
            transition: all 0.3s ease;
            position: relative;
            background: #f8f9fa;
            border-bottom: 2px solid transparent !important;
            opacity: 0.6;
            color: #6c757d !important;
        }

        #medTab .nav-link.active {
            background: #fff !important;
            opacity: 1;
            color: var(--dh-navy) !important;
            border-bottom: 3px solid var(--dh-navy) !important;
        }

        #medTab .nav-link:hover:not(.active) {
            background: #f1f3f5;
            opacity: 0.8;
        }

        /* Indicador animado para el tab activo */
        #medTab .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--dh-beige);
            animation: slideIn .3s ease-out;
        }

        @keyframes slideIn {
            from { width: 0; left: 50%; }
            to { width: 100%; left: 0; }
        }

        .tab-content {
            border: 1px solid #eee;
            border-top: none;
            border-radius: 0 0 20px 20px;
            background: #fff;
        }
    </style>
</head>
    <!-- Encabezado Móvil -->
    <div class="mobile-header d-md-none p-3 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold text-white"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h4>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Menú Lateral -->
    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-info text-dark mt-2">Veterinario</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/veterinario/dashboard">
                <i class="fas fa-chart-pie"></i> Mi Panel
            </a>
            <a href="<?= URL_BASE ?>/veterinario/pacientes" class="active"><i class="fas fa-dog"></i> Gestión de Pacientes</a>
            <a href="<?= URL_BASE ?>/veterinario/consultas"><i class="fas fa-stethoscope"></i> Consultas Médicas</a>
            <a href="<?= URL_BASE ?>/veterinario/citas"><i class="fas fa-calendar-check"></i> Gestión de Citas</a>
            <a href="<?= URL_BASE ?>/veterinario/vacunas"><i class="fas fa-syringe"></i> Control de Vacunas</a>
            <a href="#" id="enlace-mis-notas" onclick="mostrarPanelNotas(); marcarActivoSidebar(this); return false;">
                <i class="fas fa-sticky-note"></i> Mis Notas
            </a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2 mx-3" style="border-radius: 10px; padding: 12px;" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger d-flex align-items-center justify-content-center gap-2 mb-4 mx-3" style="border-radius: 10px; padding: 12px;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-end mb-2">
            <div class="user-profile text-muted d-flex align-items-center">
                <span><?php echo (isset($_SESSION['sexo']) && $_SESSION['sexo'] == 'F') ? 'Dra.' : 'Dr.'; ?> <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <div id="contenido-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?= URL_BASE ?>/veterinario/pacientes" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Volver a mis pacientes
                </a>
                <div class="user-profile text-muted d-flex align-items-center gap-3">
                    <a href="<?= URL_BASE ?>/veterinario/paciente/exportar-expediente?id=<?= $datos['ID_Mascota'] ?>" target="_blank" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-file-medical-alt me-2"></i> Exportar Expediente
                    </a>
                    <div class="ms-2">
                        <span class="fw-bold">Expediente Clínico</span>
                        <i class="fas fa-file-medical fs-3 ms-2 text-primary"></i>
                    </div>
                </div>
            </div>

        <div class="row g-4">
            <!-- Columna Izquierda: Perfil -->
            <div class="col-lg-4">
                <div class="profile-card sticky-top" style="top: 20px;">
                    <?php 
                        $foto_path = URL_BASE . "/public/uploads/pets/" . $datos['ID_Mascota'];
                        $foto_real = APP_PATH . "/public/uploads/pets/" . $datos['ID_Mascota'];
                        $exts = ['jpg', 'jpeg', 'png'];
                        $foto_url = URL_BASE . "/public/images/mascota_default.png";
                        foreach($exts as $ex) {
                            if(file_exists($foto_real . "." . $ex)) {
                                $foto_url = $foto_path . "." . $ex;
                                break;
                            }
                        }
                    ?>
                    <button class="btn-edit-float" data-bs-toggle="modal" data-bs-target="#modalEditarPaciente" title="Editar Información">
                        <i class="fas fa-edit"></i>
                    </button>
                    <img src="<?= $foto_url ?>" class="pet-header-img" alt="<?= htmlspecialchars($datos['Nombre']) ?>">
                    
                    <div class="p-4">
                        <h2 class="fw-bold mb-3" style="color: var(--dh-navy);"><?= htmlspecialchars($datos['Nombre']) ?></h2>
                        
                        <div class="mb-3">
                            <p class="text-muted small mb-2 text-uppercase fw-bold ls-1">Información Básica</p>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-muted small"><i class="fas fa-dna me-2"></i>Especie:</span>
                                <span class="fw-bold"><?= htmlspecialchars($datos['Especie']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-muted small"><i class="fas fa-tag me-2"></i>Raza:</span>
                                <span class="fw-bold"><?= htmlspecialchars($datos['Raza'] ?? 'Mestizo') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-muted small"><i class="fas fa-calendar-alt me-2"></i>Edad:</span>
                                <span class="fw-bold"><?= $datos['Edad'] ?> años</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-muted small"><i class="fas fa-weight me-2"></i>Peso:</span>
                                <span class="fw-bold"><?= number_format($datos['Peso'], 2) ?> kg</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-muted small"><i class="fas fa-palette me-2"></i>Color:</span>
                                <span class="fw-bold"><?= htmlspecialchars($datos['Color'] ?? 'S/D') ?></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-muted d-block mb-1 small"><i class="fas fa-fingerprint me-2"></i>Rasgo Distintivo:</span>
                                <p class="fw-bold mb-0 small text-dark p-2 bg-light rounded" style="border-left: 4px solid var(--dh-navy);">
                                    <?= htmlspecialchars($datos['Rasgos'] ?: 'Ninguno registrado.') ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($historial)): ?>
                            <div class="text-center mt-3">
                                <div class="badge bg-light text-primary border p-2 w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-size: 0.85rem;">
                                    <i class="fas fa-shield-halved"></i>
                                    <span>Datos validados por veterinario</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Historial y Vacunas (Tabs) -->
            <div class="col-lg-8">
                <div class="history-card p-0 overflow-hidden mb-4">
                    <div class="card-header bg-white p-0 border-0">
                        <ul class="nav nav-tabs nav-justified border-0" id="medTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-3 fw-bold border-0 rounded-0" id="consultas-tab" data-bs-toggle="tab" data-bs-target="#consultas" type="button" role="tab">
                                    <i class="fas fa-stethoscope me-2"></i>Consultas Médicas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3 fw-bold border-0 rounded-0" id="vacunas-tab" data-bs-toggle="tab" data-bs-target="#vacunas" type="button" role="tab">
                                    <i class="fas fa-syringe me-2"></i>Vacunación
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="medTabContent">
                        <!-- Tab Consultas -->
                        <div class="tab-pane fade show active" id="consultas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">Fecha</th>
                                            <th class="py-3">Motivo / Diagnóstico</th>
                                            <th class="py-3">Médico / Clínica</th>
                                            <th class="pe-4 py-3 text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($historial)): ?>
                                            <?php foreach ($historial as $h): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="d-block fw-bold"><?= date('d/m/Y', strtotime($h['Fecha_Consulta'])) ?></span>
                                                        <small class="text-muted"><?= date('H:i', strtotime($h['Fecha_Consulta'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="d-block fw-bold"><?= htmlspecialchars($h['Motivo']) ?></span>
                                                        <small class="text-muted"><?= htmlspecialchars(substr($h['Diagnostico'] ?? '', 0, 45)) ?>...</small>
                                                    </td>
                                                    <td>
                                                        <span class="d-block fw-bold small">Dr. <?= htmlspecialchars($h['Nombre_Vet'] . " " . $h['Apellido_Vet']) ?></span>
                                                        <small class="text-primary fw-bold d-block"><?= htmlspecialchars($h['Clinica'] ?: 'S/D') ?></small>
                                                    </td>
                                                    <td class="pe-4 text-center">
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="verDetalleConsulta(<?= $h['ID_Consulta'] ?>)">
                                                            <i class="fas fa-search-plus me-1"></i>Detalles
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <i class="fas fa-folder-open fs-2 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No hay consultas registradas para este paciente.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Vacunas -->
                        <div class="tab-pane fade" id="vacunas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">Fecha Aplicación</th>
                                            <th class="py-3">Vacuna / Producto</th>
                                            <th class="py-3">Próximo Refuerzo</th>
                                            <th class="pe-4 py-3 text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vacunas)): ?>
                                            <?php foreach ($vacunas as $v): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="fw-bold"><?= date('d/m/Y', strtotime($v['Fecha_Aplicacion'])) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="d-block fw-bold text-success"><?= htmlspecialchars($v['Nombre_Vacuna']) ?></span>
                                                        <small class="text-muted">Lote: <?= htmlspecialchars($v['Lote_Vacuna'] ?: 'N/A') ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($v['Fecha_Refuerzo']): ?>
                                                            <span class="badge bg-light text-danger border rounded-pill">
                                                                <i class="fas fa-calendar-check me-1"></i><?= date('d/m/Y', strtotime($v['Fecha_Refuerzo'])) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted small">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="pe-4 text-center">
                                                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="verDetalleVacunacion(<?= $v['ID_Vacunacion'] ?>)">
                                                            <i class="fas fa-search-plus me-1"></i>Detalles
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <i class="fas fa-syringe fs-2 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No hay registros de vacunación.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL EDITAR PACIENTE -->
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/veterinario/paciente/actualizar" method="POST">
                    <input type="hidden" name="id_mascota" value="<?= $datos['ID_Mascota'] ?>">
                    <div class="modal-header bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Actualizar Información Médica</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Especie</label>
                                <select name="id_especie" id="edit_especie" class="form-select border-2" onchange="cargarRazas(this.value)" required>
                                    <?php foreach($especies as $e): ?>
                                        <option value="<?= $e['ID_Especie'] ?>" <?= $e['ID_Especie'] == $datos['ID_Especie'] ? 'selected' : '' ?>><?= htmlspecialchars($e['Nombre_Especie']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Raza</label>
                                <select name="id_raza" id="edit_raza" class="form-select border-2" required>
                                    <option value="<?= $datos['ID_Raza'] ?>"><?= htmlspecialchars($datos['Raza'] ?? 'Seleccione...') ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Edad (Años)</label>
                                <input type="number" name="edad" class="form-control border-2" value="<?= $datos['Edad'] ?>" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Peso (kg)</label>
                                <input type="number" step="0.01" name="peso" class="form-control border-2" value="<?= $datos['Peso'] ?>" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Color</label>
                                <select name="id_color" class="form-select border-2" required>
                                    <?php foreach($colores as $c): ?>
                                        <option value="<?= $c['ID_Color'] ?>" <?= $c['ID_Color'] == $datos['ID_Color'] ? 'selected' : '' ?>><?= htmlspecialchars($c['Nombre_Color']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Rasgos Distintivos</label>
                                <textarea name="rasgos" class="form-control border-2" rows="2"><?= htmlspecialchars($datos['Rasgos'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-5 shadow">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE VACUNA -->
    <div class="modal fade" id="modalDetalleVacuna" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-syringe me-2"></i> Detalle de Aplicación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="detalleVacunaContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE CONSULTA -->
    <div class="modal fade" id="modalDetalleConsulta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-navy text-white p-4" style="background-color: #1a2d40;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice me-2"></i> Reporte Detallado de Consulta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="detalleConsultaContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            </div>
        </div>

        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>
    </div>

    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }

        $(document).ready(function() {
            <?php if(isset($_GET['success'])): ?>
                let msg = 'Operación realizada con éxito.';
                if('<?= $_GET['success'] ?>' == 'update') msg = 'Información del paciente actualizada correctamente.';
                Swal.fire('¡Listo!', msg, 'success');
            <?php endif; ?>
            
            // Cargar razas inicialmente si hay especie
            const espId = $('#edit_especie').val();
            if(espId) cargarRazas(espId, <?= $datos['ID_Raza'] ?: 'null' ?>);
        });

        function cargarRazas(especieId, seleccionado = null) {
            if(!especieId) return;
            console.log('Cargando razas para especie:', especieId);
            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/obtener-razas?id_especie=' + especieId,
                type: 'GET',
                dataType: 'json',
                success: function(razas) {
                    console.log('Razas recibidas:', razas);
                    let options = '<option value="">Seleccione raza...</option>';
                    if (razas && razas.length > 0) {
                        razas.forEach(r => {
                            // Soporte flexible para nombres de columnas (mayus/minus)
                            const id = r.ID_Raza || r.id_raza;
                            const nombre = r.Nombre_Raza || r.nombre_raza;
                            options += `<option value="${id}" ${seleccionado == id ? 'selected' : ''}>${nombre}</option>`;
                        });
                    } else {
                        console.warn('No se encontraron razas para esta especie.');
                    }
                    $('#edit_raza').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX Razas:', error);
                    console.error('Respuesta:', xhr.responseText);
                }
            });
        }

        function verDetalleConsulta(id) {
            $('#modalDetalleConsulta').modal('show');
            $('#detalleConsultaContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            
            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/consulta/detalle?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        const d = resp.data;
                        let html = `
                            <div class="p-4" style="background: #f8f9fa;">
                                <div class="row g-4">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-3">1. Datos del Paciente</h6>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="p-3 rounded-circle bg-primary text-white" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-dog fs-4"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-0">${d.Nombre_Mascota}</h5>
                                                <span class="text-muted small">${d.Especie} / ${d.Raza}</span>
                                            </div>
                                        </div>
                                        <p class="mb-1 small"><strong>Sexo:</strong> ${d.Sexo_Mascota == 'M' ? 'Macho' : 'Hembra'}</p>
                                        <p class="mb-0 small"><strong>Edad:</strong> ${d.Edad_Mascota} años</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-3">2. Responsable de la Mascota</h6>
                                        <p class="mb-1"><strong>Propietario:</strong> ${d.Nombre_Cuidador} ${d.Apellido_Cuidador}</p>
                                        <p class="mb-1"><strong>Cédula:</strong> ${d.Cedula_Cuidador}</p>
                                        <p class="mb-0"><strong>Contacto:</strong> ${d.Telefono_Cuidador}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-white">
                                <h6 class="fw-bold text-uppercase small text-muted mb-4">3. Evaluación Facultativa</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="p-3 rounded-4 bg-light border">
                                            <h6 class="fw-bold small mb-2 text-primary text-uppercase">Motivo de la Atención</h6>
                                            <p class="mb-0 fw-bold" style="font-size: 1.1rem;">${d.Motivo_Consulta}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 bg-light border h-100">
                                            <h6 class="fw-bold small mb-2 text-primary text-uppercase">Sintomatología</h6>
                                            <p class="mb-0 text-muted">${d.Sintomas || 'No se registraron síntomas iniciales.'}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <div class="text-center p-2 rounded border bg-white">
                                                    <small class="text-muted d-block">PESO</small>
                                                    <span class="fw-bold">${d.Peso_KG}kg</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center p-2 rounded border bg-white">
                                                    <small class="text-muted d-block">TEMP</small>
                                                    <span class="fw-bold">${d.Temperatura_C}°C</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center p-2 rounded border bg-white">
                                                    <small class="text-muted d-block">FC</small>
                                                    <span class="fw-bold">${d.Frecuencia_Cardiaca}bpm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border h-100" style="background:#fff9f0;">
                                            <h6 class="fw-bold text-warning-emphasis mb-2"><i class="fas fa-notes-medical me-2"></i>Diagnóstico</h6>
                                            <p class="mb-0 text-dark small" style="white-space: pre-wrap;">${d.Diagnostico}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border h-100" style="background:#f0fff4;">
                                            <h6 class="fw-bold text-success-emphasis mb-2"><i class="fas fa-pills me-2"></i>Tratamiento Sugerido</h6>
                                            <p class="mb-0 text-dark small" style="white-space: pre-wrap;">${d.Tratamiento_Sugerido}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 rounded-4 border" style="background:#f8f9fa;">
                                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-user-shield me-2"></i>Observaciones Privadas (Solo Veterinarios)</h6>
                                            <p class="mb-0 text-secondary small" style="white-space: pre-wrap; font-style: italic;">${d.Observaciones_Privadas || 'Sin observaciones privadas registradas.'}</p>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-uppercase small text-muted mb-3">Evidencias Adjuntas</h6>
                                <div class="row g-2 mb-4">
                                    ${d.evidencias.length > 0 ? d.evidencias.map(e => `
                                        <div class="col-md-4">
                                            <a href="${e}" target="_blank">
                                                <img src="${e}" class="img-fluid rounded border shadow-sm" style="height: 120px; width: 100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    `).join('') : '<div class="col-12"><p class="text-muted p-3 border rounded text-center small">No hay evidencias.</p></div>'}
                                </div>

                                <div class="row border-top pt-3 mt-2 text-muted">
                                    <div class="col-6 small">Médico: <strong>Dr. ${d.Nombre_Vet} ${d.Apellido_Vet}</strong></div>
                                    <div class="col-6 small text-end">Clínica: <strong>${d.Clinica}</strong></div>
                                </div>
                            </div>
                        `;
                        $('#detalleConsultaContent').html(html);
                    }
                },
                error: function() {
                    $('#detalleConsultaContent').html('<p class="p-5 text-center">Error de conexión.</p>');
                }
            });
        }
        function verDetalleVacunacion(id) {
            $('#modalDetalleVacuna').modal('show');
            $('#detalleVacunaContent').html('<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div></div>');
            
            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/vacuna/detalle?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        const d = resp.data;
                        let html = `
                            <div class="mb-4">
                                <h5 class="fw-bold text-success mb-1">${d.Nombre_Vacuna}</h5>
                                <p class="text-muted small">${d.Descripcion || ''}</p>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block uppercase small">FECHA APLICACIÓN</small>
                                    <p class="fw-bold">${new Date(d.Fecha_Aplicacion).toLocaleDateString()}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block uppercase small">PRÓX. REFUERZO</small>
                                    <p class="fw-bold text-danger">${d.Fecha_Refuerzo ? new Date(d.Fecha_Refuerzo).toLocaleDateString() : 'N/A'}</p>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block uppercase small">LOTE / REGISTRO</small>
                                    <p class="fw-bold">${d.Lote_Vacuna || 'N/A'}</p>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block uppercase small">OBSERVACIONES</small>
                                    <p class="bg-light p-2 rounded border small">${d.Observaciones || 'Sin observaciones.'}</p>
                                </div>
                                <div class="col-12 border-top pt-3 mt-2">
                                    <p class="small text-muted mb-0">Médico: <strong>Dr. ${d.Nombre_Vet} ${d.Apellido_Vet}</strong></p>
                                </div>
                            </div>
                        `;
                        $('#detalleVacunaContent').html(html);
                    } else {
                        $('#detalleVacunaContent').html('<p class="p-4 text-center">Error al cargar datos.</p>');
                    }
                }
            });
        }
    </script>
</body>
</html>
