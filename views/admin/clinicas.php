<?php
require_once '../../config/auth_check.php';
require_once '../../config/db.php';
require_once '../../models/Clinica.php';

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../login.php?error=acceso_denegado");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Traer TODAS las sucursales del admin (activas e inactivas)
$stmt = $db->prepare("SELECT * FROM clinicas WHERE ID_Admin = :id ORDER BY Nombre_Sucursal ASC");
$stmt->bindParam(':id', $_SESSION['id_perfil']);
$stmt->execute();
$sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Sucursales — DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        /* Buscador a la IZQUIERDA */
        div.dataTables_wrapper div.dataTables_filter {
            text-align: left !important;
            margin-bottom: 16px;
        }
        .dataTables_filter label {
            display: flex; align-items: center; gap: 8px;
            font-weight: 600; color: #495057; font-size: 0.88rem;
        }
        .dataTables_filter input {
            width: 260px !important;
            background-color: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 20px !important;
            padding: 7px 14px !important;
            outline: none !important;
            transition: border-color 0.3s, box-shadow 0.3s;
            margin-left: 6px !important;
        }
        .dataTables_filter input:focus {
            border-color: var(--dh-beige) !important;
            box-shadow: 0 0 0 3px rgba(197,170,127,0.25) !important;
        }
        div.dataTables_wrapper div.dataTables_length { display: none; }
        .dataTables_info, .dataTables_paginate {
            font-size: 0.85rem; color: #6c757d; margin-top: 15px;
        }

        /* Formularios en modales */
        #formCrear .form-control,
        #formCrear .form-select  { border: 1px solid #dee2e6 !important; }
        #formGestionar .form-control,
        #formGestionar .form-select { border: 1px solid #dee2e6 !important; }
        #formGestionar .border-danger { border-color: #dc3545 !important; }
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
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color:var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>
        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/admin/dashboard"><i class="fas fa-chart-pie"></i> Mi Resumen</a>
            <a href="<?= URL_BASE ?>/views/admin/clinicas.php" class="active"><i class="fas fa-hospital"></i> Mis Sucursales</a>
            <a href="<?= URL_BASE ?>/views/admin/registrar_vet.php"><i class="fas fa-user-md"></i> Veterinarios</a>
            <a href="<?= URL_BASE ?>/views/admin/reportes.php"><i class="fas fa-file-medical-alt"></i> Reportes Clínicos</a>
        </nav>
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2"
               style="border-radius:10px;padding:12px;margin:0 15px;border-color:rgba(255,255,255,0.2);"
               data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i><span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2"
               style="border-radius:10px;padding:12px;margin:0 15px 20px;width:auto !important;">
                <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">

        <!-- Bienvenida -->
        <div class="d-flex justify-content-end mb-2">
            <div class="text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <!-- Encabezado + botón crear -->
        <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-0" style="color:var(--dh-navy);">Mis Sucursales</h2>
                <p class="text-muted mt-1">Gestión de tus ubicaciones clínicas</p>
            </div>
            <button class="btn text-white" style="background-color:var(--dh-navy);"
                    data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-plus me-1"></i> Nueva Sucursal
            </button>
        </div>

        <!-- Tabla -->
        <div class="card border-0 shadow-sm p-4" style="border-radius:15px;">
            <div class="table-responsive">
                <table id="tablaSucursales" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Sucursal</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sucursales as $s): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($s['Nombre_Sucursal']) ?></td>
                            <td><?= htmlspecialchars($s['Direccion']) ?></td>
                            <td><?= htmlspecialchars($s['Telefono'] ?? '—') ?></td>
                            <td>
                                <?php if ($s['Estado'] === 'Activa'): ?>
                                    <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Activa</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold"><i class="fas fa-ban"></i> Suspendida</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-gestionar"
                                    data-id="<?= $s['ID_Clinica'] ?>"
                                    data-nombre="<?= htmlspecialchars($s['Nombre_Sucursal']) ?>"
                                    data-direccion="<?= htmlspecialchars($s['Direccion']) ?>"
                                    data-telefono="<?= htmlspecialchars($s['Telefono'] ?? '') ?>"
                                    data-rnc="<?= htmlspecialchars($s['RNC'] ?? '') ?>"
                                    data-estado="<?= $s['Estado'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalGestionar">
                                    <i class="fas fa-cog"></i> Gestionar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== MODAL CREAR SUCURSAL ===== -->
    <div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
                <form id="formCrear">
                    <div class="modal-header text-white" style="background-color:var(--dh-navy);border-radius:20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-hospital me-2"></i> Nueva Sucursal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label class="form-label">Nombre de la Sucursal</label>
                                <input type="text" name="nombre_sucursal" id="c_nombre"
                                    class="form-control" placeholder="Ej: Clínica Norte" required>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="c_telefono"
                                    class="form-control mascara-tel-clinica"
                                    maxlength="12" placeholder="809-000-0000" inputmode="numeric" required>
                                <small id="msg_tel_crear" class="text-danger d-none fw-bold" style="font-size:0.75rem;">Teléfono incompleto</small>
                            </div>

                            <!-- RNC -->
                            <div class="col-md-6">
                                <label class="form-label">RNC <small class="text-muted">(9 dígitos)</small></label>
                                <input type="text" name="rnc" id="c_rnc"
                                    class="form-control mascara-rnc-clinica"
                                    maxlength="11" placeholder="000-00000-0" inputmode="numeric" required>
                                <small id="msg_rnc_crear" class="text-danger d-none fw-bold" style="font-size:0.75rem;">RNC incompleto (9 dígitos)</small>
                            </div>

                            <!-- Dirección -->
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="c_direccion"
                                    class="form-control" placeholder="Calle, No., Sector y Ciudad" required>
                            </div>

                            <!-- Contraseña admin -->
                            <div class="col-md-12 mt-1">
                                <label class="form-label text-danger fw-bold">
                                    <i class="fas fa-lock"></i> Contraseña Admin (Requerida)
                                </label>
                                <input type="password" name="contrasena_admin" id="c_pass"
                                    class="form-control border-danger" required
                                    placeholder="Ingrese su contraseña de Administrador">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius:0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="btnCrear" class="btn btn-primary px-4" disabled>
                            <i class="fas fa-plus me-1"></i> Crear Sucursal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== MODAL GESTIONAR SUCURSAL ===== -->
    <div class="modal fade" id="modalGestionar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
                <form id="formGestionar">
                    <input type="hidden" name="id_clinica" id="g_id">
                    <input type="hidden" name="estado"     id="g_estado_hidden">

                    <div class="modal-header text-white" style="background-color:var(--dh-navy);border-radius:20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-hospital me-2"></i> Gestionar Sucursal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label class="form-label">Nombre de la Sucursal</label>
                                <input type="text" name="nombre_sucursal" id="g_nombre" class="form-control" required>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="g_telefono"
                                    class="form-control mascara-tel-clinica"
                                    maxlength="12" placeholder="809-000-0000" required>
                                <small id="msg_tel_gest" class="text-danger d-none fw-bold" style="font-size:0.75rem;">Teléfono incompleto</small>
                            </div>

                            <!-- RNC -->
                            <div class="col-md-6">
                                <label class="form-label">RNC</label>
                                <input type="text" name="rnc" id="g_rnc"
                                    class="form-control mascara-rnc-clinica" maxlength="11" required>
                                <small id="msg_rnc_gest" class="text-danger d-none fw-bold" style="font-size:0.75rem;">RNC incompleto</small>
                            </div>

                            <!-- Dirección -->
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="g_direccion" class="form-control" required>
                            </div>

                            <!-- Contraseña admin -->
                            <div class="col-md-12 mt-1">
                                <label class="form-label text-danger fw-bold">
                                    <i class="fas fa-lock"></i> Contraseña Admin (Requerida)
                                </label>
                                <input type="password" name="contrasena_admin" id="g_pass"
                                    class="form-control border-danger" required
                                    placeholder="Ingrese su contraseña de Administrador">
                            </div>

                            <!-- Zona de Riesgo -->
                            <div class="col-md-12">
                                <div class="mt-1 p-3 rounded" style="background-color:#fff3f3;border:1px dashed #dc3545;">
                                    <h6 class="fw-bold text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle"></i> Zona de Riesgo
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 text-dark fw-bold" id="g_estado_texto">Estado: Activa</p>
                                            <small class="text-muted">Suspender desactiva el acceso de los veterinarios de esta sucursal.</small>
                                        </div>
                                        <button type="button" id="btn_toggle_estado"
                                            class="btn btn-warning text-dark fw-bold"
                                            onclick="toggleEstadoClinica()">
                                            <i class="fas fa-ban"></i> Suspender
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius:0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="btnGuardarGest" class="btn btn-primary px-4" disabled>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
    $(document).ready(function () {

        // ── DataTables ──────────────────────────────────────────────────────
        $('#tablaSucursales').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
                search: "<i class='fas fa-search me-1'></i> Buscar:",
                paginate: { next: 'Siguiente', previous: 'Anterior' }
            },
            pageLength: 10,
            dom: '<"d-flex align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        // ── Máscaras ────────────────────────────────────────────────────────
        $(document).on('input', '.mascara-tel-clinica', function () {
            let v = $(this).val().replace(/\D/g, '').substring(0, 10);
            if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{4})$/, '$1-$2-$3');
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})$/, '$1-$2');
            $(this).val(v);
        });

        $(document).on('input', '.mascara-rnc-clinica', function () {
            let v = $(this).val().replace(/\D/g, '').substring(0, 9);
            if (v.length > 8) v = v.replace(/^(\d{3})(\d{5})(\d{1})$/, '$1-$2-$3');
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,5})$/, '$1-$2');
            $(this).val(v);
        });

        // ── MODAL CREAR: validación ─────────────────────────────────────────
        $('#formCrear').on('input', validarCrear);

        $('#btnCrear').on('click', function () {
            Swal.fire({
                title: '¿Crear sucursal?',
                text: 'Se registrará una nueva sucursal en el sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar'
            }).then(function (r) { if (r.isConfirmed) enviarCrear(); });
        });

        // ── MODAL GESTIONAR: poblar con relatedTarget ───────────────────────
        $('#modalGestionar').on('show.bs.modal', function (event) {
            const btn = $(event.relatedTarget);

            $('#g_id').val(btn.data('id'));
            $('#g_nombre').val(btn.data('nombre'));
            $('#g_direccion').val(btn.data('direccion'));
            $('#g_telefono').val(btn.data('telefono'));
            $('#g_rnc').val(btn.data('rnc'));

            const estado = btn.data('estado');
            $('#g_estado_hidden').val(estado);
            actualizarZonaRiesgoClinica(estado);

            $('#g_pass').val('');
            $('#btnGuardarGest').prop('disabled', true);
        });

        $('#modalGestionar').on('hidden.bs.modal', function () {
            $('#g_pass').val('');
            $('#btnGuardarGest').prop('disabled', true);
        });

        $('#formGestionar').on('input', validarGestionar);

        $('#btnGuardarGest').on('click', function () {
            Swal.fire({
                title: '¿Guardar cambios?',
                text: 'Los datos de la sucursal serán actualizados.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then(function (r) { if (r.isConfirmed) enviarGestionar(); });
        });
    });

    // ── Zona de Riesgo ──────────────────────────────────────────────────────
    function actualizarZonaRiesgoClinica(estado) {
        const esActiva = (estado === 'Activa');
        if (esActiva) {
            $('#g_estado_texto').html('Estado: <span class="text-success">Activa</span>');
            $('#btn_toggle_estado').html('<i class="fas fa-ban"></i> Suspender')
                .removeClass('btn-success').addClass('btn-warning text-dark');
        } else {
            $('#g_estado_texto').html('Estado: <span class="text-danger">Suspendida</span>');
            $('#btn_toggle_estado').html('<i class="fas fa-check"></i> Reactivar')
                .removeClass('btn-warning text-dark').addClass('btn-success');
        }
    }

    function toggleEstadoClinica() {
        const actual = $('#g_estado_hidden').val();
        const nuevo  = (actual === 'Activa') ? 'Inactiva' : 'Activa';
        $('#g_estado_hidden').val(nuevo);
        actualizarZonaRiesgoClinica(nuevo);
        validarGestionar();
    }

    // ── Validaciones ────────────────────────────────────────────────────────
    function validarCrear() {
        const nombre = $('#c_nombre').val().trim();
        const tel    = $('#c_telefono').val();
        const rnc    = $('#c_rnc').val();
        const dir    = $('#c_direccion').val().trim();
        const pass   = $('#c_pass').val();

        const telOk = tel.length === 12;
        const rncOk = rnc.replace(/\D/g, '').length === 9;

        $('#msg_tel_crear').toggleClass('d-none', telOk || tel === '');
        $('#msg_rnc_crear').toggleClass('d-none', rncOk || rnc === '');

        const ok = nombre.length >= 3 && telOk && rncOk && dir.length >= 3 && pass !== '';
        $('#btnCrear').prop('disabled', !ok);
    }

    function validarGestionar() {
        const nombre = $('#g_nombre').val().trim();
        const tel    = $('#g_telefono').val();
        const rnc    = $('#g_rnc').val();
        const dir    = $('#g_direccion').val().trim();
        const pass   = $('#g_pass').val();

        const telOk = tel.length === 12;
        const rncOk = rnc.replace(/\D/g, '').length === 9;

        $('#msg_tel_gest').toggleClass('d-none', telOk || tel === '');
        $('#msg_rnc_gest').toggleClass('d-none', rncOk || rnc === '');

        const ok = nombre.length >= 3 && telOk && rncOk && dir.length >= 3 && pass !== '';
        $('#btnGuardarGest').prop('disabled', !ok);
    }

    // ── AJAX: Crear ─────────────────────────────────────────────────────────
    function enviarCrear() {
        const btn = document.getElementById('btnCrear');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';

        fetch('../../controllers/admin/ClinicaController.php?action=registrar', {
            method: 'POST',
            body: new FormData(document.getElementById('formCrear'))
        })
        .then(r => r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error('Respuesta inesperada.'); } }))
        .then(data => {
            if (data.status === 'success') {
                $('#modalCrear').modal('hide');
                Swal.fire({ icon: 'success', title: '¡Creada!', text: 'Sucursal registrada correctamente.', showConfirmButton: false, timer: 1500 })
                .then(() => location.reload());
            } else {
                let msg = 'Error al crear la sucursal.';
                if (data.type === 'auth_admin_fallida') msg = 'Contraseña de administrador incorrecta.';
                else if (data.type === 'campos_incompletos') msg = 'Completa todos los campos.';
                else if (data.type === 'rnc_duplicado') msg = 'Este RNC ya está registrado en otra de tus sucursales.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1A2D40' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus me-1"></i> Crear Sucursal';
                document.getElementById('c_pass').value = '';
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error Crítico', text: err.message });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Crear Sucursal';
        });
    }

    // ── AJAX: Gestionar/Actualizar ──────────────────────────────────────────
    function enviarGestionar() {
        const btn = document.getElementById('btnGuardarGest');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        fetch('../../controllers/admin/ClinicaController.php?action=actualizar', {
            method: 'POST',
            body: new FormData(document.getElementById('formGestionar'))
        })
        .then(r => r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error('Respuesta inesperada.'); } }))
        .then(data => {
            if (data.status === 'success') {
                $('#modalGestionar').modal('hide');
                Swal.fire({ icon: 'success', title: '¡Actualizada!', text: 'Datos de la sucursal actualizados.', showConfirmButton: false, timer: 1500 })
                .then(() => location.reload());
            } else {
                let msg = 'Error al actualizar.';
                if (data.type === 'auth_admin_fallida') msg = 'Contraseña de administrador incorrecta.';
                else if (data.type === 'campos_incompletos') msg = 'Completa todos los campos.';
                else if (data.type === 'acceso_denegado') msg = 'Acceso denegado.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1A2D40' });
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
                document.getElementById('g_pass').value = '';
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error Crítico', text: err.message });
            btn.disabled = false;
            btn.innerHTML = 'Guardar Cambios';
        });
    }
    </script>

</body>
</html>
