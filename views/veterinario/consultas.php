<?php 
require_once APP_PATH . '/config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/veterinario/consultas");
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: none;
        }
        .btn-detail {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-detail:hover {
            background-color: var(--dh-navy);
            color: white;
            border-color: var(--dh-navy);
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
            <span class="badge bg-info text-dark mt-2">Veterinario</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/veterinario/dashboard">
                <i class="fas fa-chart-pie"></i> Mi Panel
            </a>
            <a href="<?= URL_BASE ?>/veterinario/pacientes"><i class="fas fa-dog"></i> Gestión de Pacientes</a>
            <a href="<?= URL_BASE ?>/veterinario/consultas" class="active"><i class="fas fa-stethoscope"></i> Consultas Médicas</a>
            <a href="<?= URL_BASE ?>/veterinario/citas"><i class="fas fa-calendar-check"></i> Gestión de Citas</a>
            <a href="<?= URL_BASE ?>/veterinario/vacunas"><i class="fas fa-syringe"></i> Control de Vacunas</a>
            <a href="#" onclick="mostrarPanelNotas(); marcarActivoSidebar(this); return false;">
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
            <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Historial de Consultas</h2>
                    <p class="text-muted mt-1">Registro cronológico de tus atenciones médicas</p>
                </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaConsulta">
                <i class="fas fa-plus-circle me-2"></i> Nueva Consulta
            </button>
        </div>

        <div class="table-card mt-4">
            <div class="table-responsive">
                <table id="tablaConsultas" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Mascota</th>
                            <th>Cuidador</th>
                            <th>Motivo / Diag.</th>
                            <th>Clínica</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($consultas)): ?>
                            <?php foreach ($consultas as $c): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($c['Fecha_Consulta'])) ?></div>
                                        <small class="text-muted"><?= date('h:i A', strtotime($c['Fecha_Consulta'])) ?></small>
                                    </td>
                                    <td class="fw-bold" style="color: var(--dh-navy);"><?= htmlspecialchars($c['Nombre_Mascota']) ?></td>
                                    <td>
                                        <div class="fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($c['Nombre_Cuidador'] . " " . $c['Apellido_Cuidador']) ?></div>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.75rem;"><?= htmlspecialchars($c['Cedula_Cuidador']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($c['Motivo_Consulta']) ?></div>
                                        <div class="text-truncate text-muted small" style="max-width: 150px;">
                                            <?= htmlspecialchars($c['Diagnostico'] ?: 'Sin diagnóstico') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-hospital-alt me-1 text-primary"></i>
                                            <?= htmlspecialchars($c['Clinica']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" style="border-radius: 8px;">
                                            <button class="btn btn-light border btn-sm px-3" onclick="verDetalleConsulta(<?= $c['ID_Consulta'] ?>)" title="Ver Detalles">
                                                <i class="fas fa-search-plus text-primary"></i>
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-light border btn-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-download text-success"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    <li><a class="dropdown-item" href="<?= URL_BASE ?>/veterinario/consulta/exportar?id=<?= $c['ID_Consulta'] ?>&formato=pdf" target="_blank"><i class="fas fa-file-pdf text-danger me-2"></i> Exportar PDF</a></li>
                                                    <li><a class="dropdown-item" href="<?= URL_BASE ?>/veterinario/consulta/exportar?id=<?= $c['ID_Consulta'] ?>&formato=excel" target="_blank"><i class="fas fa-file-excel text-success me-2"></i> Exportar Excel</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>
    </div>

    <!-- Modal Nueva Consulta -->
    <div class="modal fade" id="modalNuevaConsulta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/veterinario/consulta/registrar" method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-stethoscope me-2"></i> Registrar Nueva Consulta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase">Cédula Cuidador</label>
                                <div class="input-group">
                                    <input type="text" id="cedula_cuidador" class="form-control border-2" placeholder="Buscar..." required>
                                    <button class="btn btn-outline-primary border-2" type="button" onclick="buscarCuidador()"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase">Nombre Cuidador</label>
                                <input type="text" id="nombre_cuidador" class="form-control bg-light border-2" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase">Paciente</label>
                                <select name="id_mascota" id="select_id_mascota" class="form-select border-2" required disabled>
                                    <option value="">Cédula primero...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase">Motivo Principal</label>
                                <input type="text" name="motivo" class="form-control border-2" required placeholder="Ej: Dolor...">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Síntomas Reportados</label>
                                <textarea name="sintomas" class="form-control border-2" rows="2" placeholder="Describa lo que el cuidador reporta..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Peso (KG)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="peso" class="form-control border-2" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Temperatura (°C)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="temperatura" class="form-control border-2">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">FC (BPM)</label>
                                <div class="input-group">
                                    <input type="number" name="frecuencia" class="form-control border-2">
                                    <span class="input-group-text">lpm</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control border-2" rows="3" required placeholder="Ingrese el diagnóstico clínico..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tratamiento Sugerido</label>
                                <textarea name="tratamiento" class="form-control border-2" rows="3" required placeholder="Medicamentos, dosis..."></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-primary">Evidencias Fotográficas</label>
                                <div class="p-3 border-2 border-dashed rounded-3 bg-light text-center">
                                    <input type="file" name="evidencias[]" multiple class="file-custom-input" id="evidencias">
                                    <p class="text-muted small mt-2 mb-0">Puede seleccionar múltiples imágenes. Límite: 20MB.</p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Observaciones Privadas</label>
                                <textarea name="observaciones" class="form-control border-2" rows="2" placeholder="Solo para el personal médico..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Guardar Consulta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Consulta -->
    <div class="modal fade" id="modalDetalleConsulta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-medical-alt me-2"></i> Reporte Detallado de Consulta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="detalleConsultaContent">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Cargando expediente...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>

    <script>
        $(document).ready(function() {
            $('#tablaConsultas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                responsive: true
            });

            <?php if(isset($_GET['success'])): ?>
                Swal.fire('¡Éxito!', 'Consulta registrada correctamente.', 'success');
            <?php endif; ?>

            $('#cedula_cuidador').on('blur', function() {
                if($(this).val().length >= 5) buscarCuidador();
            });
        });

        function buscarCuidador() {
            const cedula = $('#cedula_cuidador').val();
            if(!cedula) return;

            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/buscar-cuidador?cedula=' + cedula,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        $('#nombre_cuidador').val(resp.cuidador.Nombre + ' ' + resp.cuidador.Apellido);
                        
                        let options = '<option value="">Seleccione mascota...</option>';
                        resp.mascotas.forEach(m => {
                            options += `<option value="${m.ID_Mascota}">${m.Nombre}</option>`;
                        });
                        $('#select_id_mascota').html(options).prop('disabled', false);
                    } else {
                        Swal.fire('Atención', resp.message, 'warning');
                        $('#nombre_cuidador').val('');
                        $('#select_id_mascota').html('<option value="">No hay datos</option>').prop('disabled', true);
                    }
                }
            });
        }

        function verDetalleConsulta(id) {
            $('#modalDetalleConsulta').modal('show');
            $('#detalleConsultaContent').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');
            
            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/consulta/detalle?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        const d = resp.data;
                        let html = `
                            <div class="p-4 bg-light border-bottom">
                                <div class="row">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-3">1. Información del Paciente</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white p-2 rounded-circle shadow-sm">
                                                <i class="fas fa-dog fs-2 text-primary"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-0">${d.Nombre_Mascota}</h5>
                                                <p class="text-muted small mb-0">${d.Especie} / ${d.Raza}</p>
                                                <span class="badge bg-secondary small">${d.Edad_Mascota} años | ${d.Sexo_Mascota == 'M' ? 'Macho' : 'Hembra'}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ps-md-4">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-3">2. Información del Cuidador</h6>
                                        <p class="mb-1 fw-bold">${d.Nombre_Cuidador} ${d.Apellido_Cuidador}</p>
                                        <p class="mb-1 small text-muted"><i class="fas fa-id-card me-2"></i>Cédula: ${d.Cedula_Cuidador}</p>
                                        <p class="mb-0 small text-muted"><i class="fas fa-phone me-2"></i>Teléfono: ${d.Telefono_Cuidador}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4">
                                <h6 class="fw-bold text-uppercase small text-muted mb-4">3. Detalles de la Consulta</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <div class="p-3 bg-white border rounded-4 shadow-sm">
                                            <h5 class="fw-bold text-primary mb-2">${d.Motivo_Consulta}</h5>
                                            <p class="mb-0 text-muted">${d.Sintomas || 'No se registraron síntomas iniciales.'}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-metric p-3 rounded-4 bg-white border">
                                            <small class="text-muted d-block mb-1">PESO</small>
                                            <h4 class="fw-bold mb-0">${d.Peso_KG} <small>kg</small></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-metric p-3 rounded-4 bg-white border">
                                            <small class="text-muted d-block mb-1">TEMP.</small>
                                            <h4 class="fw-bold mb-0">${d.Temperatura_C} <small>°C</small></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-metric p-3 rounded-4 bg-white border">
                                            <small class="text-muted d-block mb-1">FC</small>
                                            <h4 class="fw-bold mb-0">${d.Frecuencia_Cardiaca} <small>lpm</small></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border" style="background:#fff9f0;">
                                            <h6 class="fw-bold text-warning-emphasis mb-2"><i class="fas fa-notes-medical me-2"></i>Diagnóstico</h6>
                                            <p class="mb-0 text-dark" style="white-space: pre-wrap;">${d.Diagnostico}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border" style="background:#f0fff4;">
                                            <h6 class="fw-bold text-success-emphasis mb-2"><i class="fas fa-pills me-2"></i>Tratamiento Sugerido</h6>
                                            <p class="mb-0 text-dark" style="white-space: pre-wrap;">${d.Tratamiento_Sugerido}</p>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-uppercase small text-muted mb-4">Evidencias Adjuntas</h6>
                                <div class="row g-2 mb-4">
                                    ${d.evidencias.length > 0 ? d.evidencias.map(e => `
                                        <div class="col-md-4 col-lg-3">
                                            <a href="${e}" target="_blank">
                                                <img src="${e}" class="img-fluid rounded-3 border shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    `).join('') : '<div class="col-12"><p class="text-muted p-4 border rounded-4 text-center">No se adjuntaron evidencias para esta consulta.</p></div>'}
                                </div>

                                <div class="row border-top pt-4 mt-2">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-uppercase small text-muted">4. Profesional Médico</h6>
                                        <p class="mb-0 fw-bold">Dr. ${d.Nombre_Vet} ${d.Apellido_Vet}</p>
                                        <p class="small text-muted mb-0">Exequatur: ${d.Exequatur || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <h6 class="fw-bold text-uppercase small text-muted">5. Centro de Atención</h6>
                                        <p class="mb-0 fw-bold">${d.Clinica}</p>
                                        <p class="small text-muted mb-0">${d.Direccion_Clinica}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#detalleConsultaContent').html(html);
                    } else {
                        $('#detalleConsultaContent').html('<p class="p-5 text-center">Error al cargar datos.</p>');
                    }
                }
            });
        }

        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
