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
<body>

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
            <span class="badge bg-success text-white mt-2">Cuidador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/cuidador/dashboard"><i class="fas fa-home"></i> Mi Panel</a>
            <a href="<?= URL_BASE ?>/cuidador/mis-mascotas" class="active"><i class="fas fa-bone"></i> Mis Mascotas</a>
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
        
        <!-- Contenedor para ocultar al mostrar notas -->
        <div id="contenido-dashboard">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?= URL_BASE ?>/cuidador/mis-mascotas" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Volver a mis mascotas
                </a>
                <a href="<?= URL_BASE ?>/cuidador/mascota/exportar-expediente?id=<?= $datos['ID_Mascota'] ?>" target="_blank" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: var(--dh-navy); border: none;">
                    <i class="fas fa-file-medical-alt me-2"></i>Exportar Expediente
                </a>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <!-- Tarjeta de Perfil de Mascota -->
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        <div class="position-relative" style="height: 300px; overflow: hidden; background: #f0f2f5;">
                            <?php 
                                $foto_path = 'public/images/default_pet.png'; // Fallback
                                $id_m = $datos['ID_Mascota'];
                                // Buscar posibles extensiones
                                if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pets' . DIRECTORY_SEPARATOR . $id_m . '.jpg')) $foto_path = "public/uploads/pets/$id_m.jpg";
                                elseif (file_exists(APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pets' . DIRECTORY_SEPARATOR . $id_m . '.jpeg')) $foto_path = "public/uploads/pets/$id_m.jpeg";
                                elseif (file_exists(APP_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pets' . DIRECTORY_SEPARATOR . $id_m . '.png')) $foto_path = "public/uploads/pets/$id_m.png";
                            ?>
                            <img id="mascotaFotoGrande" src="<?= URL_BASE ?>/<?= $foto_path ?>?v=<?= time() ?>" class="w-100 h-100" style="object-fit: cover;">
                            
                            <!-- Botón para actualizar imagen -->
                            <button class="btn btn-dark btn-sm position-absolute bottom-0 end-0 m-3 shadow" 
                                    style="border-radius: 10px; opacity: 0.85; background: rgba(0,0,0,0.7); border: none;"
                                    data-bs-toggle="modal" data-bs-target="#modalActualizarFoto">
                                <i class="fas fa-camera me-1"></i> Actualizar Foto
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <h2 class="fw-bold mb-3" style="color: var(--dh-navy);"><?= htmlspecialchars($datos['Nombre']) ?></h2>
                            <div class="d-flex flex-column gap-2 mt-4">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted"><i class="fas fa-dna me-2"></i>Especie:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($datos['Especie']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted"><i class="fas fa-tag me-2"></i>Raza:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($datos['Raza'] ?? 'Mestizo') ?></span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Edad:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($datos['Edad']) ?> años</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted"><i class="fas fa-weight me-2"></i>Peso:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($datos['Peso']) ?> kg</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted"><i class="fas fa-palette me-2"></i>Color:</span>
                                    <span class="fw-bold"><?= htmlspecialchars($datos['Color'] ?? 'S/D') ?></span>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted d-block mb-1"><i class="fas fa-fingerprint me-2"></i>Rasgo Distintivo:</span>
                                    <p class="fw-bold mb-0 small text-dark p-2 bg-light rounded" style="border-left: 4px solid var(--dh-navy);">
                                        <?= htmlspecialchars($datos['Rasgos'] ?: 'Ninguno registrado.') ?>
                                    </p>
                                </div>

                                <?php if (!empty($historial)): ?>
                                    <div class="mt-3 text-center">
                                        <div class="badge bg-light text-primary border p-2 w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-size: 0.85rem;">
                                            <i class="fas fa-shield-halved"></i>
                                            <span>Datos validados por veterinario</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Expediente Médico (Tabs) -->
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white p-0 border-0 overflow-hidden" style="border-radius: 20px 20px 0 0;">
                            <ul class="nav nav-tabs nav-justified border-0" id="medTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-3 fw-bold border-0 rounded-0" id="consultas-tab" data-bs-toggle="tab" data-bs-target="#consultas" type="button" role="tab" style="color: var(--dh-navy);">
                                        <i class="fas fa-stethoscope me-2"></i>Consultas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3 fw-bold border-0 rounded-0" id="vacunas-tab" data-bs-toggle="tab" data-bs-target="#vacunas" type="button" role="tab" style="color: var(--dh-navy);">
                                        <i class="fas fa-syringe me-2"></i>Vacunación
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content" id="medTabContent">
                                <!-- Tab Consultas -->
                                <div class="tab-pane fade show active" id="consultas" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4 py-3">Fecha</th>
                                                    <th class="py-3">Motivo / Diagnóstico</th>
                                                    <th class="py-3">Veterinario / Clínica</th>
                                                    <th class="pe-4 py-3 text-center">Detalles</th>
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
                                                                <small class="text-muted"><?= htmlspecialchars(substr($h['Diagnostico'], 0, 40)) ?>...</small>
                                                            </td>
                                                            <td>
                                                                <span class="d-block fw-bold small"><?= htmlspecialchars($h['Nombre_Vet'] . " " . $h['Apellido_Vet']) ?></span>
                                                                <small class="text-success fw-bold d-block"><?= htmlspecialchars($h['Clinica'] ?: 'S/D') ?></small>
                                                            </td>
                                                            <td class="pe-4 text-center">
                                                                <button class="btn btn-sm btn-outline-primary rounded-circle" 
                                                                        title="Ver detalles" onclick="verDetalleConsulta(<?= $h['ID_Consulta'] ?>)">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-5">
                                                            <p class="text-muted mb-0">No hay consultas registradas.</p>
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
                                                    <th class="ps-4 py-3">Fecha</th>
                                                    <th class="py-3">Vacuna</th>
                                                    <th class="py-3">Refuerzo</th>
                                                    <th class="pe-4 py-3 text-center">Detalles</th>
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
                                                                <small class="text-muted">Dr. <?= htmlspecialchars($v['Nombre_Vet']) ?></small>
                                                            </td>
                                                            <td>
                                                                <?php if($v['Fecha_Refuerzo']): ?>
                                                                    <span class="badge bg-light text-danger border">
                                                                        <?= date('d/m/Y', strtotime($v['Fecha_Refuerzo'])) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">No requiere</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="pe-4 text-center">
                                                                <button class="btn btn-sm btn-outline-success rounded-circle" 
                                                                        title="Ver detalles" onclick="verDetalleVacuna(<?= $v['ID_Vacunacion'] ?>)">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-5">
                                                            <p class="text-muted mb-0">No hay vacunas registradas.</p>
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

        </div><!-- /#contenido-dashboard -->

        <!-- Panel de Notas (SPA) -->
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>

    </div>

    </div>
    
    <!-- Modal Actualizar Foto -->
    <div class="modal fade" id="modalActualizarFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: var(--dh-navy); border: none;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-camera me-2"></i>Actualizar Foto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formActualizarFoto" enctype="multipart/form-data">
                        <input type="hidden" name="id_mascota" value="<?= $datos['ID_Mascota'] ?>">
                        <div class="text-center mb-3">
                            <label for="nuevaFotoInput" class="btn btn-outline-primary w-100 p-4" style="border-style: dashed; border-radius: 15px;">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i><br>
                                <span class="fw-bold">Seleccionar nueva imagen</span><br>
                                <small class="text-muted">JPG, PNG (máx 2MB)</small>
                            </label>
                            <input type="file" id="nuevaFotoInput" name="foto" class="d-none" accept="image/jpeg, image/png" onchange="validarYNuevaPreview(this)">
                        </div>
                        <div id="nuevaPreviewContainer" class="d-none text-center mt-3">
                            <img id="nuevaImgPreview" src="#" class="img-fluid rounded shadow" style="max-height: 150px; border: 3px solid #eee;">
                            <p class="text-muted small mt-2" id="nuevaInfoFoto"></p>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light border me-2" data-bs-dismiss="modal" style="border-radius:10px;">Cancelar</button>
                            <button type="submit" id="btnGuardarFoto" class="btn btn-primary px-4" style="background-color: var(--dh-navy); border-radius:10px; opacity:0.5;" disabled>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
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

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
    <script>
        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }

        function verDetalleConsulta(id) {
            $('#modalDetalleConsulta').modal('show');
            $('#detalleConsultaContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            
            $.ajax({
                url: '<?= URL_BASE ?>/cuidador/mascota/consulta-detalle?id=' + id,
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
                                        <h6 class="fw-bold text-uppercase small text-muted mb-3">2. Médico / Clínica</h6>
                                        <p class="mb-1 text-primary fw-bold">Dr. ${d.Nombre_Vet} ${d.Apellido_Vet}</p>
                                        <p class="mb-1"><strong>Sede:</strong> ${d.Clinica}</p>
                                        <p class="mb-0"><strong>Tel:</strong> ${d.Telefono_Clinica}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-white">
                                <h6 class="fw-bold text-uppercase small text-muted mb-4">3. Evaluación Médica</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="p-3 rounded-4 bg-light border">
                                            <h6 class="fw-bold small mb-2 text-primary text-uppercase">Motivo</h6>
                                            <p class="mb-0 fw-bold">${d.Motivo_Consulta}</p>
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
                                </div>

                                <h6 class="fw-bold text-uppercase small text-muted mb-3">Evidencias Adjuntas</h6>
                                <div class="row g-2 mb-2">
                                    ${d.evidencias.length > 0 ? d.evidencias.map(e => `
                                        <div class="col-md-4">
                                            <a href="${e}" target="_blank">
                                                <img src="${e}" class="img-fluid rounded border shadow-sm" style="height: 120px; width: 100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    `).join('') : '<div class="col-12"><p class="text-muted p-2 border rounded text-center small">No hay evidencias visuales.</p></div>'}
                                </div>
                            </div>
                        `;
                        $('#detalleConsultaContent').html(html);
                    }
                },
                error: function() {
                    $('#detalleConsultaContent').html('<p class="p-5 text-center">Error al conectar con el servidor.</p>');
                }
            });
        }

        function verDetalleVacuna(id) {
            $('#modalDetalleVacuna').modal('show');
            $('#detalleVacunaContent').html('<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div></div>');
            
            $.ajax({
                url: '<?= URL_BASE ?>/cuidador/mascota/vacuna-detalle?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        const d = resp.data;
                        let html = `
                            <div class="mb-4">
                                <h5 class="fw-bold text-success mb-1">${d.Nombre_Vacuna}</h5>
                                <p class="text-muted small">${d.Descripcion || 'Sin descripción disponible.'}</p>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold">FECHA APLICACIÓN</small>
                                    <p class="fw-bold">${new Date(d.Fecha_Aplicacion).toLocaleDateString()}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold">PRÓX. REFUERZO</small>
                                    <p class="fw-bold text-danger">${d.Fecha_Refuerzo ? new Date(d.Fecha_Refuerzo).toLocaleDateString() : 'No aplica'}</p>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small fw-bold">LOTE / REGISTRO</small>
                                    <p class="fw-bold mb-2">${d.Lote_Vacuna || 'N/A'}</p>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small fw-bold">OBSERVACIONES</small>
                                    <p class="bg-light p-2 rounded border small mb-0">${d.Observaciones || 'Sin observaciones.'}</p>
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

        function validarYNuevaPreview(input) {
            const btn = document.getElementById('btnGuardarFoto');
            const preview = document.getElementById('nuevaImgPreview');
            const container = document.getElementById('nuevaPreviewContainer');
            const info = document.getElementById('nuevaInfoFoto');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const allowed = ['image/jpeg', 'image/jpg', 'image/png'];

                if (!allowed.includes(file.type) || file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo no válido',
                        text: 'Asegúrate de que sea JPG/PNG y pese menos de 2MB.',
                        confirmButtonColor: '#1A2D40'
                    });
                    input.value = "";
                    btn.disabled = true;
                    btn.style.opacity = "0.5";
                    container.classList.add('d-none');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('d-none');
                    info.textContent = `${file.name} (${sizeMB}MB)`;
                    btn.disabled = false;
                    btn.style.opacity = "1";
                }
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('formActualizarFoto')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnGuardarFoto');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

            const formData = new FormData(this);
            fetch('<?= URL_BASE ?>/cuidador/mascota/actualizar-foto', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'La foto se ha guardado correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    btn.disabled = false;
                    btn.innerHTML = 'Guardar Cambios';
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error de Red' });
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            });
        });
    </script>
</body>
</html>
