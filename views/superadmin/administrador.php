<?php
require_once '../../config/auth_check.php';

// SEGURIDAD: Evitar acceso sin sesión
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Obtener el rol de la base de datos para el saludo
$nombre_rol = "Super Admin";
try {
    $stmt_rol = $db->prepare("SELECT Nombre_Rol FROM roles WHERE ID_Rol = :id_rol");
    $stmt_rol->bindParam(':id_rol', $_SESSION['id_rol']);
    $stmt_rol->execute();
    $resultado_rol = $stmt_rol->fetchColumn();
    if ($resultado_rol)
        $nombre_rol = $resultado_rol;
} catch (PDOException $e) {
}

// CONSULTA MAESTRA (Trae datos de administrador, usuarios y clinicas)
$query = "SELECT a.ID_Admin, a.Nombre, a.Apellido, a.Telefono, a.Cedula, u.ID_Usuario, u.Correo, u.Estado, c.Nombre_Sucursal, c.RNC, c.Direccion 
          FROM administrador a
          INNER JOIN usuarios u ON a.ID_Usuario = u.ID_Usuario
          LEFT JOIN clinicas c ON a.ID_Admin = c.ID_Admin
          ORDER BY a.ID_Admin DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        .card-dh {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .btn-dh-navy {
            background-color: var(--dh-navy);
            color: white;
            border-radius: 8px;
            border: none;
            padding: 10px 20px;
        }

        .btn-dh-navy:hover {
            background-color: #253d56;
            color: white;
        }
    </style>
</head>

<body>

        <!-- Encabezado Móvil (Solo visible en pantallas pequeñas) -->
    <div class="mobile-header d-md-none p-3 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold text-white"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h4>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Menú Lateral -->
    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella
            </h3>
            <span class="badge bg-warning text-dark mt-2"><?= htmlspecialchars($nombre_rol) ?></span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/superadmin/dashboard"><i class="fas fa-chart-pie"></i> Estadísticas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/administrador.php" class="active"><i class="fas fa-hospital"></i>
                Gestión de Clínicas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/reportes.php"><i class="fas fa-file-export"></i> Gestión de
                Reportes</a>
        </nav>

        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2"
                style="border-radius: 10px; padding: 12px; margin: 0 15px; border-color: rgba(255,255,255,0.2);"
                data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout"
                class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2"
                style="border-radius: 10px; padding: 12px; margin: 0 15px 20px; width: auto !important;">
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
            <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Administradores de Franquicias</h2>
            <p class="text-muted mt-1">Gestión y control de clientes</p>
        </div>

        <div class="d-flex justify-content-end mb-3 flex-wrap gap-2">
            <button class="btn btn-dh-navy shadow" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                <i class="fas fa-plus-circle me-2"></i> Nueva Franquicia
            </button>
        </div>

        <div class="card-dh mt-2">
            <div class="table-responsive">
                <table id="tablaAdmins" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Administrador</th>
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Clínica</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><strong>#<?php echo $admin['ID_Admin']; ?></strong></td>
                                <td><?php echo htmlspecialchars($admin['Nombre'] . " " . $admin['Apellido']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($admin['Cedula']); ?></td>
                                <td><?php echo htmlspecialchars($admin['Telefono'] ?? '---'); ?></td>
                                <td>
                                    <span
                                        class="badge <?php echo isset($admin['Nombre_Sucursal']) ? 'bg-info text-dark' : 'bg-secondary'; ?>">
                                        <?php echo htmlspecialchars($admin['Nombre_Sucursal'] ?? 'Sin Clínica'); ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?php echo htmlspecialchars($admin['Correo']); ?></td>
                                <td>
                                    <?php if (isset($admin['Estado']) && $admin['Estado'] == 'Activo'): ?>
                                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold"><i class="fas fa-ban"></i> Suspendido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary btn-editar"
                                        data-id="<?php echo $admin['ID_Admin']; ?>"
                                        data-idusu="<?php echo $admin['ID_Usuario']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($admin['Nombre']); ?>"
                                        data-apellido="<?php echo htmlspecialchars($admin['Apellido']); ?>"
                                        data-telefono="<?php echo htmlspecialchars($admin['Telefono']); ?>"
                                        data-cedula="<?php echo htmlspecialchars($admin['Cedula']); ?>"
                                        data-correo="<?php echo htmlspecialchars($admin['Correo']); ?>"
                                        data-clinica="<?php echo htmlspecialchars($admin['Nombre_Sucursal']); ?>"
                                        data-rnc="<?php echo htmlspecialchars($admin['RNC']); ?>"
                                        data-direccion="<?php echo htmlspecialchars($admin['Direccion']); ?>"
                                        data-estado="<?php echo isset($admin['Estado']) ? $admin['Estado'] : 'Activo'; ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditar">
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

    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form id="formRegistro" action="../../controllers/superadmin/AdminController.php?action=registrar"
                    method="POST">
                    <div class="modal-header text-white"
                        style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-store-alt me-2"></i> Registrar Nueva Franquicia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2">1. Credenciales y Perfil
                            </h6>
                            <div class="col-md-6"><label class="form-label">Correo Electrónico</label><input
                                    type="email" name="correo" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Contraseña Temporal</label><input
                                    type="password" name="contrasena" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">Nombre</label><input type="text"
                                    name="nombre" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">Apellido</label><input type="text"
                                    name="apellido" class="form-control" required></div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="reg_telefono"
                                    class="form-control mascara-telefono" maxlength="12" placeholder="809-000-0000"
                                    inputmode="numeric" required>
                                <small id="msg_telefono" class="text-danger d-none fw-bold"
                                    style="font-size: 0.75rem;">Faltan números</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula" id="reg_cedula" class="form-control mascara-cedula"
                                    maxlength="13" placeholder="000-0000000-0" inputmode="numeric" required>
                                <small id="msg_cedula" class="text-danger d-none fw-bold"
                                    style="font-size: 0.75rem;">Debe tener 11 dígitos</small>
                            </div>

                            <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2 mt-4">2. Información de la
                                Clínica</h6>
                            <div class="col-md-6"><label class="form-label">Nombre Veterinaria</label><input type="text"
                                    name="nombre_clinica" id="reg_clinica" class="form-control" required></div>

                            <div class="col-md-6">
                                <label class="form-label">RNC</label>
                                <input type="text" name="rnc" id="reg_rnc" class="form-control mascara-rnc"
                                    maxlength="11" placeholder="Solo números" inputmode="numeric" required>
                                <small id="msg_rnc" class="text-danger d-none fw-bold" style="font-size: 0.75rem;">Debe
                                    tener 11 dígitos exactos</small>
                            </div>

                            <div class="col-md-12"><label class="form-label">Dirección Física</label><input type="text"
                                    name="direccion_clinica" id="reg_direccion" class="form-control" required></div>

                            <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2 mt-4">3. Seguridad Master
                                Key</h6>
                            <div class="col-md-12">
                                <label class="form-label text-danger fw-bold"><i class="fas fa-lock"></i> Contraseña
                                    Maestra</label>
                                <input type="password" name="admin_auth" id="reg_auth"
                                    class="form-control border-danger" required
                                    placeholder="Confirme con su contraseña de SuperAdmin">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="btnGuardarRegistro" class="btn btn-dh-navy px-4"
                            onclick="confirmarAccion('formRegistro', 'registrar')" disabled>Guardar Franquicia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form id="formEditar" method="POST">
                    <input type="hidden" name="id_admin" id="edit_id_admin">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario">

                    <div class="modal-header text-white"
                        style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-user-cog me-2"></i> Gestionar Franquicia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nombre</label><input type="text"
                                    name="nombre" id="edit_nombre" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Apellido</label><input type="text"
                                    name="apellido" id="edit_apellido" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text"
                                    name="telefono" id="edit_telefono" class="form-control mascara-telefono"
                                    maxlength="12" placeholder="809-000-0000" inputmode="numeric" required></div>
                            <div class="col-md-6"><label class="form-label">Clínica</label><input type="text"
                                    name="nombre_clinica" id="edit_clinica" class="form-control" required></div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label text-danger fw-bold"><i class="fas fa-lock"></i> Contraseña
                                    Maestra (Requerida)</label>
                                <input type="password" name="admin_auth" class="form-control border-danger" required
                                    placeholder="Ingrese su contraseña SuperAdmin">
                            </div>
                        </div>

                        <div class="mt-4 p-3 rounded" style="background-color: #fff3f3; border: 1px dashed #dc3545;">
                            <h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle"></i> Zona de
                                Riesgo</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0 text-dark fw-bold" id="estado_actual_texto">Estado: Activo</p>
                                    <small class="text-muted">Suspender desactiva el acceso. Eliminar borra todos los
                                        datos.</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btn_suspender" class="btn btn-warning text-dark fw-bold"
                                        onclick="confirmarAccion('formEditar', 'suspender')">
                                        <i class="fas fa-ban"></i> Suspender
                                    </button>
                                    <button type="button" id="btn_eliminar" class="btn btn-danger fw-bold d-none"
                                        onclick="confirmarAccion('formEditar', 'eliminar')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary px-4" style="background-color: #0d6efd;"
                            onclick="confirmarAccion('formEditar', 'editar')">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializar DataTables con el texto personalizado
            $('#tablaAdmins').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
                    "info": "Mostrando página _PAGE_ de _PAGES_ con un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando página 0 de 0 con un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)"
                },
                "pageLength": 10
            });

            // Lógica del botón "Gestionar" para llenar el Modal
            $('.btn-editar').on('click', function () {
                const btn = $(this);
                const estado = btn.data('estado');

                $('#edit_id_admin').val(btn.data('id'));
                $('#edit_id_usuario').val(btn.data('idusu'));
                $('#edit_nombre').val(btn.data('nombre'));
                $('#edit_apellido').val(btn.data('apellido'));
                $('#edit_telefono').val(btn.data('telefono'));
                $('#edit_clinica').val(btn.data('clinica'));

                // Control visual de Suspender/Eliminar
                if (estado === 'Inactivo' || estado === 'Suspendido') {
                    $('#estado_actual_texto').html('Estado: <span class="text-danger">Suspendido</span>');
                    $('#btn_suspender').html('<i class="fas fa-check"></i> Reactivar').removeClass('btn-warning').addClass('btn-success');
                    $('#btn_eliminar').removeClass('d-none'); // Muestra el botón rojo
                } else {
                    $('#estado_actual_texto').html('Estado: <span class="text-success">Activo</span>');
                    $('#btn_suspender').html('<i class="fas fa-ban"></i> Suspender').removeClass('btn-success').addClass('btn-warning');
                    $('#btn_eliminar').addClass('d-none'); // Oculta el botón rojo
                }
            });
        });

        // Función de SweetAlert2 para manejar TODAS las acciones
        function confirmarAccion(formId, accion) {
            let titulo = ""; let texto = ""; let icono = "info";
            let colorConfirmar = "#1A2D40"; let textoConfirmar = "Sí, proceder";

            // Asignar el action correcto al formulario
            document.getElementById(formId).action = "../../controllers/superadmin/AdminController.php?action=" + accion;

            switch (accion) {
                case 'registrar':
                    titulo = "¿Crear nueva franquicia?";
                    texto = "Se generarán accesos y perfiles en el sistema.";
                    icono = "question";
                    break;
                case 'editar':
                    titulo = "¿Guardar cambios?";
                    texto = "Los datos del cliente serán actualizados.";
                    icono = "warning";
                    colorConfirmar = "#ffc107"; // Amarillo
                    textoConfirmar = "Sí, guardar";
                    break;
                case 'suspender':
                    titulo = "¿Cambiar estado de acceso?";
                    texto = "El usuario perderá o recuperará el acceso al sistema.";
                    icono = "warning";
                    colorConfirmar = "#dc3545"; // Rojo
                    textoConfirmar = "Sí, cambiar estado";
                    break;
                case 'eliminar':
                    titulo = "¡PELIGRO: Borrado Definitivo!";
                    texto = "Se borrará todo el historial. Esta acción NO se puede deshacer.";
                    icono = "error";
                    colorConfirmar = "#dc3545"; // Rojo
                    textoConfirmar = "Sí, ELIMINAR TODO";
                    break;
            }

            Swal.fire({
                title: titulo,
                text: texto,
                icon: icono,
                showCancelButton: true,
                confirmButtonColor: colorConfirmar,
                cancelButtonColor: '#6c757d',
                confirmButtonText: textoConfirmar,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    <script>

        function soloNumerosYFormato(e) {
            const teclasPermitidas = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete', 'Enter'];
            // Bloquear si intentan escribir algo que no sea número ni tecla de control
            if (!/^[0-9]$/.test(e.key) && !teclasPermitidas.includes(e.key) && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
            }
        }

        // Aplicar a TODOS los inputs de Teléfono 
        document.querySelectorAll('.mascara-telefono').forEach(function (input) {
            input.addEventListener('keydown', soloNumerosYFormato);
            input.addEventListener('input', function (e) {
                let numero = e.target.value.replace(/\D/g, '');
                let formateado = '';
                if (numero.length > 0) formateado += numero.substring(0, 3);
                if (numero.length >= 4) formateado += '-' + numero.substring(3, 6);
                if (numero.length >= 7) formateado += '-' + numero.substring(6, 10);
                e.target.value = formateado;
            });
        });

        // Aplicar a TODOS los inputs de Cédula
        document.querySelectorAll('.mascara-cedula').forEach(function (input) {
            input.addEventListener('keydown', soloNumerosYFormato);
            input.addEventListener('input', function (e) {
                let numero = e.target.value.replace(/\D/g, '');
                let formateado = '';
                if (numero.length > 0) formateado += numero.substring(0, 3);
                if (numero.length >= 4) formateado += '-' + numero.substring(3, 10);
                if (numero.length >= 11) formateado += '-' + numero.substring(10, 11);
                e.target.value = formateado;
            });
        });

        // Aplicar a TODOS los inputs de RNC (Solo números, máx 11)
        document.querySelectorAll('.mascara-rnc').forEach(function (input) {
            input.addEventListener('keydown', soloNumerosYFormato);
            input.addEventListener('input', function (e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 11);
            });
        });
    </script>

    <script>
        // 🛡️ --- NOTIFICACIONES Y ERRORES --- 🛡️
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const exito = urlParams.get('exito');

            // Manejo de Errores
            if (error) {
                let mensaje = "Ocurrió un error inesperado al procesar la solicitud.";
                if (error === 'correo_duplicado') mensaje = "Ese correo electrónico ya está registrado en DocuHuella.";
                if (error === 'cedula_duplicada') mensaje = "La cédula ingresada ya pertenece a un administrador existente.";
                if (error === 'rnc_duplicado') mensaje = "El RNC ingresado ya pertenece a otra clínica registrada.";
                if (error === 'fallo_registro') mensaje = "Hubo un problema de conexión al guardar los datos.";

                if (error === 'clave_requerida') mensaje = "Debe ingresar su contraseña maestra (SuperAdmin) para proceder.";
                if (error === 'clave_incorrecta') mensaje = "La contraseña maestra ingresada es incorrecta. Acción denegada.";

                Swal.fire({
                    icon: 'error',
                    title: 'Acción Denegada',
                    text: mensaje,
                    confirmButtonColor: '#1A2D40'
                });

                // Limpiar URL sin recargar la página
                window.history.replaceState(null, null, window.location.pathname);
            }

            // Manejo de Éxitos
            if (exito) {
                let mensajeExito = "Operación realizada con éxito.";
                if (exito === 'franquicia_creada') mensajeExito = "La nueva franquicia y sus accesos fueron creados.";
                if (exito === 'franquicia_actualizada') mensajeExito = "Los datos se actualizaron correctamente.";
                if (exito === 'estado_cambiado') mensajeExito = "El estado de acceso fue modificado.";
                if (exito === 'franquicia_eliminada') mensajeExito = "La franquicia fue eliminada definitivamente del sistema.";

                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: mensajeExito,
                    timer: 3000,
                    showConfirmButton: false
                });

                // Limpiar URL sin recargar la página
                window.history.replaceState(null, null, window.location.pathname);
            }
        });
    </script>

    <script>
        // 🛡️ --- VALIDACIÓN EN TIEMPO REAL DEL MODAL DE REGISTRO --- 🛡️
        document.addEventListener('DOMContentLoaded', function () {
            const formRegistro = document.getElementById('formRegistro');
            const btnGuardar = document.getElementById('btnGuardarRegistro');
            const inputsRequeridos = formRegistro.querySelectorAll('input[required]');

            // Elementos específicos
            const inputTelefono = document.getElementById('reg_telefono');
            const inputCedula = document.getElementById('reg_cedula');
            const inputRNC = document.getElementById('reg_rnc');

            const msgTelefono = document.getElementById('msg_telefono');
            const msgCedula = document.getElementById('msg_cedula');
            const msgRNC = document.getElementById('msg_rnc');

            function validarFormulario() {
                let todoLleno = true;
                let formatosCorrectos = true;

                // 1. Verificar que NINGÚN campo requerido esté vacío
                inputsRequeridos.forEach(input => {
                    if (input.value.trim() === '') {
                        todoLleno = false;
                    }
                });

                // 2. Validar longitud del Teléfono (Debe ser 12: XXX-XXX-XXXX)
                if (inputTelefono.value.length > 0 && inputTelefono.value.length < 12) {
                    msgTelefono.classList.remove('d-none');
                    formatosCorrectos = false;
                } else {
                    msgTelefono.classList.add('d-none');
                }

                // 3. Validar longitud de la Cédula (Debe ser 13: XXX-XXXXXXX-X)
                if (inputCedula.value.length > 0 && inputCedula.value.length < 13) {
                    msgCedula.classList.remove('d-none');
                    formatosCorrectos = false;
                } else {
                    msgCedula.classList.add('d-none');
                }

                // 4. Validar longitud del RNC (Debe ser 11 dígitos)
                if (inputRNC.value.length > 0 && inputRNC.value.length < 11) {
                    msgRNC.classList.remove('d-none');
                    formatosCorrectos = false;
                } else {
                    msgRNC.classList.add('d-none');
                }

                // 5. Encender o apagar el botón maestro
                if (todoLleno && formatosCorrectos) {
                    btnGuardar.disabled = false;
                } else {
                    btnGuardar.disabled = true;
                }
            }

            // Poner a escuchar a todos los inputs
            inputsRequeridos.forEach(input => {
                input.addEventListener('input', validarFormulario);
            });

            // 🧹 Limpiar validaciones al cerrar el modal
            document.getElementById('modalRegistro').addEventListener('hidden.bs.modal', function () {
                formRegistro.reset();
                validarFormulario(); // Volver a apagar el botón
            });
        });
    </script>

    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>

</html>
