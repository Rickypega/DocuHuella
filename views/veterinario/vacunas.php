<?php 
require_once APP_PATH . '/config/auth_check.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/veterinario/vacunas");
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
        .vaccine-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
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
            <a href="<?= URL_BASE ?>/veterinario/consultas"><i class="fas fa-stethoscope"></i> Consultas Médicas</a>
            <a href="<?= URL_BASE ?>/veterinario/citas"><i class="fas fa-calendar-check"></i> Gestión de Citas</a>
            <a href="<?= URL_BASE ?>/veterinario/vacunas" class="active"><i class="fas fa-syringe"></i> Control de Vacunas</a>
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
                    <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Control de Vacunas</h2>
                    <p class="text-muted mt-1">Historial de inmunización y refuerzos programados</p>
                </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAplicarVacuna">
                <i class="fas fa-plus-circle me-2"></i> Aplicar Vacuna / Refuerzo
            </button>
        </div>

        <div class="table-card mt-4">
            <div class="table-responsive">
                <table id="tablaVacunas" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Mascota</th>
                            <th>Vacuna</th>
                            <th>Cuidador</th>
                            <th>Refuerzo</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vacunaciones)): ?>
                            <?php foreach ($vacunaciones as $v): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($v['Fecha_Aplicacion'])) ?></div>
                                        <small class="text-muted">Lote: <?= htmlspecialchars($v['Lote_Vacuna'] ?: 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($v['Nombre_Mascota']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($v['Nombre_Vacuna']) ?></div>
                                        <small class="text-muted">Recomendado c/<?= $v['Periodo_Refuerzo_Meses'] ?> m</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= htmlspecialchars($v['Nombre_Cui'] . " " . $v['Apellido_Cui']) ?></div>
                                    </td>
                                    <td>
                                        <?php if($v['Fecha_Refuerzo']): ?>
                                            <span class="text-danger fw-bold"><i class="fas fa-clock me-1"></i> <?= date('d/m/Y', strtotime($v['Fecha_Refuerzo'])) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin refuerzo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">Aplicada</span>
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

    <!-- Modal Aplicar Vacuna -->
    <div class="modal fade" id="modalAplicarVacuna" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/veterinario/vacuna/registrar" method="POST">
                    <div class="modal-header bg-primary text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-syringe me-2"></i> Aplicar Vacuna o Refuerzo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cédula del Cuidador</label>
                                <div class="input-group">
                                    <input type="text" id="cedula_cuidador_v" class="form-control border-2" placeholder="Buscar..." required>
                                    <button class="btn btn-outline-primary border-2" type="button" onclick="buscarCuidadorVacuna()"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cuidador</label>
                                <input type="text" id="nombre_cuidador_v" class="form-control bg-light border-2" readonly placeholder="---">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mascota</label>
                                <select name="id_mascota" id="select_id_mascota_v" class="form-select border-2" required disabled>
                                    <option value="">Primero busque al cuidador...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Vacuna a Aplicar</label>
                                <select name="id_vacuna" id="select_id_vacuna" class="form-select border-2" required onchange="actualizarInfoVacuna()">
                                    <option value="">Seleccione vacuna...</option>
                                    <?php foreach($catalogo_vacunas as $vac): ?>
                                        <option value="<?= $vac['ID_Vacuna'] ?>" data-meses="<?= $vac['Periodo_Refuerzo_Meses'] ?>"><?= htmlspecialchars($vac['Nombre_Vacuna']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div id="msg_recomendacion" class="alert alert-info border-0 rounded-3 mb-0 d-none">
                                    <i class="fas fa-info-circle me-2"></i> <span id="text_recomendacion"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha Aplicación</label>
                                <input type="date" name="fecha_aplicacion" class="form-control border-2" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">Próximo Refuerzo</label>
                                <input type="date" name="fecha_refuerzo" id="fecha_refuerzo" class="form-control border-2 border-danger-subtle">
                                <small class="text-muted">Dejar vacío si no requiere refuerzo.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Número de Lote</label>
                                <input type="text" name="lote" class="form-control border-2" placeholder="Ej: VAX-9921">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Observaciones</label>
                                <textarea name="observaciones" class="form-control border-2" rows="2" placeholder="Cualquier reacción observada o nota importante..."></textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="check_cita" checked>
                                    <label class="form-check-label fw-bold text-primary" for="check_cita">Agendar cita automática de recordatorio para el cuidador</label>
                                    <p class="text-muted small mb-0 mt-1 ms-2">Si la fecha de refuerzo está definida, el cuidador recibirá una notificación en su agenda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Registrar Vacuna</button>
                    </div>
                </form>
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
            $('#tablaVacunas').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json' },
                order: [[0, 'desc']],
                pageLength: 10
            });

            <?php if(isset($_GET['success'])): ?>
                Swal.fire('¡Éxito!', 'Vacunación registrada correctamente. Se ha enviado el recordatorio al cuidador.', 'success');
            <?php endif; ?>

            $('#cedula_cuidador_v').on('blur', function() {
                if($(this).val().length >= 5) buscarCuidadorVacuna();
            });
        });

        function buscarCuidadorVacuna() {
            const cedula = $('#cedula_cuidador_v').val();
            if(!cedula) return;

            $.ajax({
                url: '<?= URL_BASE ?>/veterinario/buscar-cuidador?cedula=' + cedula,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if(resp.status === 'success') {
                        $('#nombre_cuidador_v').val(resp.cuidador.Nombre + ' ' + resp.cuidador.Apellido);
                        let options = '<option value="">Seleccione mascota...</option>';
                        resp.mascotas.forEach(m => {
                            options += `<option value="${m.ID_Mascota}">${m.Nombre}</option>`;
                        });
                        $('#select_id_mascota_v').html(options).prop('disabled', false);
                    } else {
                        Swal.fire('Atención', resp.message, 'warning');
                        $('#nombre_cuidador_v').val('');
                        $('#select_id_mascota_v').html('<option value="">No hay datos</option>').prop('disabled', true);
                    }
                }
            });
        }

        function actualizarInfoVacuna() {
            const select = document.getElementById('select_id_vacuna');
            const selected = select.options[select.selectedIndex];
            const meses = selected.getAttribute('data-meses');
            const msgBox = document.getElementById('msg_recomendacion');

            if(meses && meses > 0) {
                $('#text_recomendacion').text(`Tiempo recomendado para refuerzo: cada ${meses} meses.`);
                msgBox.classList.remove('d-none');
            } else {
                msgBox.classList.add('d-none');
            }
        }

        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
