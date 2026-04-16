<?php 
require_once APP_PATH . '/config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/veterinario/citas");
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
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.3s;
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
            <a href="<?= URL_BASE ?>/veterinario/citas" class="active"><i class="fas fa-calendar-check"></i> Gestión de Citas</a>
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
                    <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Control de Citas</h2>
                    <p class="text-muted mt-1">Agenda y programa tus próximas atenciones</p>
                </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                <i class="fas fa-calendar-plus me-2"></i> Agendar Cita
            </button>
        </div>

        <div class="table-card mt-4">
            <div class="table-responsive">
                <table id="tablaCitas" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha / Hora</th>
                            <th>Info Cuidador</th>
                            <th>Info Mascota</th>
                            <th>Motivo</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($citas)): ?>
                            <?php foreach ($citas as $cit): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($cit['Fecha_Cita'])) ?></div>
                                        <span class="badge bg-light text-primary border"><?= date('h:i A', strtotime($cit['Hora_Cita'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($cit['Nombre_Cuidador'] . " " . $cit['Apellido_Cuidador']) ?></div>
                                        <div class="small text-muted"><i class="fas fa-id-card me-1"></i> <?= htmlspecialchars($cit['Cedula']) ?></div>
                                        <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($cit['Telefono']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--dh-navy);"><?= htmlspecialchars($cit['Nombre_Mascota']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($cit['Especie'] . " / " . $cit['Raza']) ?></div>
                                        <div class="small"><span class="badge bg-light text-dark border"><?= number_format($cit['Peso'], 2) ?> kg</span></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($cit['Motivo']) ?>">
                                            <?= htmlspecialchars($cit['Motivo']) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-outline-warning btn-action" onclick="abrirEditarCita(<?= $cit['ID_Cita'] ?>, '<?= $cit['Fecha_Cita'] ?>', '<?= $cit['Hora_Cita'] ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-action" onclick="eliminarCita(<?= $cit['ID_Cita'] ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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

    <!-- Modal Nueva Cita -->
    <div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/veterinario/cita/agendar" method="POST">
                    <div class="modal-header bg-primary text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus me-2"></i> Agendar Nueva Cita</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cédula del Cuidador</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2"><i class="fas fa-id-card"></i></span>
                                <input type="text" id="cedula_cuidador" class="form-control border-2" placeholder="Ingrese cédula..." required>
                                <button class="btn btn-outline-primary border-2" type="button" onclick="buscarCuidador()"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Nombre</label>
                                <input type="text" id="nombre_cuidador" class="form-control bg-light" readonly placeholder="---">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Apellido</label>
                                <input type="text" id="apellido_cuidador" class="form-control bg-light" readonly placeholder="---">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Paciente</label>
                            <select name="id_mascota" id="select_id_mascota" class="form-select border-2" required disabled>
                                <option value="">Busque un cuidador primero...</option>
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" name="fecha" class="form-control border-2" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Hora</label>
                                <input type="time" name="hora" class="form-control border-2" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motivo de la Cita</label>
                            <input type="text" name="motivo" class="form-control border-2" required placeholder="Ej: Cirugía programada, Seguimiento...">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Notas Adicionales</label>
                            <textarea name="notas" class="form-control border-2" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Cita (Solo fecha/hora) -->
    <div class="modal fade" id="modalEditarCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/veterinario/cita/editar" method="POST">
                    <input type="hidden" name="id_cita" id="edit_id_cita">
                    <div class="modal-header bg-warning text-dark p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Reprogramar Cita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="text-muted mb-4">Solo se permite la edición de la fecha y hora por integridad del registro inicial.</p>
                        <div class="row g-3">
                            <div class="col-6 text-start">
                                <label class="form-label fw-bold small text-uppercase">Nueva Fecha</label>
                                <input type="date" name="fecha" id="edit_fecha" class="form-control border-2" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6 text-start">
                                <label class="form-label fw-bold small text-uppercase">Nueva Hora</label>
                                <input type="time" name="hora" id="edit_hora" class="form-control border-2" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-5 shadow fw-bold">Actualizar</button>
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
            $('#tablaCitas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });

            <?php if(isset($_GET['success'])): ?>
                let msg = 'Operación realizada con éxito.';
                if('<?= $_GET['success'] ?>' == 'edit') msg = 'Cita reprogramada correctamente.';
                if('<?= $_GET['success'] ?>' == 'delete') msg = 'Cita eliminada.';
                Swal.fire('¡Listo!', msg, 'success');
            <?php endif; ?>

            // Búsqueda en tiempo real al salir del campo cédula
            $('#cedula_cuidador').on('blur', function() {
                if($(this).val().length >= 5) {
                    buscarCuidador();
                }
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
                        $('#nombre_cuidador').val(resp.cuidador.Nombre);
                        $('#apellido_cuidador').val(resp.cuidador.Apellido);
                        
                        let options = '<option value="">Seleccione mascota...</option>';
                        resp.mascotas.forEach(m => {
                            options += `<option value="${m.ID_Mascota}">${m.Nombre}</option>`;
                        });
                        $('#select_id_mascota').html(options).prop('disabled', false);
                    } else {
                        Swal.fire('Atención', resp.message, 'warning');
                        $('#nombre_cuidador, #apellido_cuidador').val('');
                        $('#select_id_mascota').html('<option value="">No hay datos</option>').prop('disabled', true);
                    }
                }
            });
        }

        function abrirEditarCita(id, fecha, hora) {
            $('#edit_id_cita').val(id);
            $('#edit_fecha').val(fecha);
            $('#edit_hora').val(hora);
            $('#modalEditarCita').modal('show');
        }

        function eliminarCita(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= URL_BASE ?>/veterinario/cita/eliminar?id=' + id;
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
