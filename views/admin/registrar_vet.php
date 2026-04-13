<?php 
require_once '../../config/auth_check.php';
require_once '../../config/db.php';
require_once '../../models/Clinica.php';
require_once '../../models/Especialidad.php';

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../login.php?error=acceso_denegado"); exit();
}

$database = new Database();
$db = $database->getConnection();
// Instanciar el modelo y obtener la lista
$clinicaModel = new Clinica($db);
$clinicaModel->id_admin = $_SESSION['id_perfil'];
$mis_sucursales = $clinicaModel->obtenerClinicasPorAdmin();
$especialidadModel = new Especialidad($db);
$lista_especialidades = $especialidadModel->obtenerTodas();

$query = "SELECT v.*, u.Correo, u.Estado, u.ID_Usuario, c.Nombre_Sucursal, e.Nombre_Especialidad
          FROM Veterinarios v
          INNER JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario
          INNER JOIN Clinicas c ON v.ID_Clinica = c.ID_Clinica
          INNER JOIN Especialidades e ON v.ID_Especialidad = e.ID_Especialidad
          WHERE c.ID_Admin = :id_admin ORDER BY v.ID_Veterinario DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_admin', $_SESSION['id_perfil']);
$stmt->execute();
$veterinarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Médica - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
    <style>
        /* Ocultar el ojo nativo de los navegadores */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        .section-header { 
            border-bottom: 2px solid var(--dh-beige); 
            color: var(--dh-navy); 
            font-weight: bold; 
            margin-bottom: 15px; 
            padding-bottom: 5px; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        /* Estilos personalizados para los Inputs redondeados */
        .input-group-text, .form-control, .btn-ojo { 
            border: none !important; 
        }

        .error-msg { 
            font-size: 0.75rem; 
            font-weight: bold; 
            margin-top: 3px; 
            display: block; 
        }

        .security-box { 
            background-color: #fff9db; 
            border: 1px solid #ffec99; 
            border-radius: 12px; 
            padding: 20px; 
        }
        /* Ajustes para el Buscador de la Tabla */
        .dataTables_filter {
            margin-bottom: 20px; /* Espacio entre el buscador y la tabla */
        }

        .dataTables_filter input {
            width: 350px !important; 
            background-color: white !important;
            border: 1px solid #dee2e6 !important; 
            border-radius: 20px !important; 
            padding: 8px 15px !important;
            margin-left: 10px !important;
            outline: none !important;
            transition: 0.3s;
        }

        .dataTables_filter input:focus {
            border-color: var(--dh-beige) !important;
            box-shadow: 0 0 0 0.25 margin rgba(197, 170, 127, 0.25) !important;
        }

        /* Ajuste de los textos de la tabla */
        .dataTables_info, .dataTables_paginate {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 15px;
        }

        .btn-vibrante {
        background-color: #28a745; /* Un verde éxito vibrante */
        color: white;
        border-radius: 20px;
        font-weight: bold;
        transition: 0.3s;
        border: none;
    }
    .btn-vibrante:hover {
        background-color: #218838;
        transform: scale(1.05);
        color: white;
    }
    .btn-vibrante:disabled {
        background-color: #6c757d;
        transform: scale(1);
    }

    </style>
</head>
<body>

   <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0">
                <i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella
            </h3>
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/admin/dashboard">
                <i class="fas fa-chart-pie"></i> Mi Resumen
            </a>
            <a href="<?= URL_BASE ?>/views/admin/clinicas.php">
                <i class="fas fa-hospital"></i> Mis Sucursales
            </a>
            <a href="<?= URL_BASE ?>/views/admin/registrar_vet.php" class="active">
                <i class="fas fa-user-md"></i> Veterinarios
            </a>
            <a href="<?= URL_BASE ?>/views/admin/reportes.php">
                <i class="fas fa-file-medical-alt"></i> Reportes Clinicos
            </a>
        </nav>
        
        <div class="mt-auto"> 
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; padding: 12px; margin: auto 15px 20px; width: auto !important;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--dh-navy);">Gestión de Veterinarios</h2>
            <button class="btn btn-dark shadow px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalRegistroVet">
                <i class="fas fa-user-plus me-2"></i> Nuevo Médico
            </button>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
            <table id="tablaVets" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Veterinario</th>
                        <th>Cédula</th>
                        <th>Teléfono</th> 
                        <th>Especialidad</th>
                        <th>Clinica</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($veterinarios as $vet): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($vet['Nombre'] . " " . $vet['Apellido']) ?></td>
                        <td><?= $vet['Cedula'] ?></td>
                        <td><?= $vet['Telefono'] ?></td>
                        <td><?= htmlspecialchars($vet['Nombre_Especialidad'] ?? 'Sin Especialidad') ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($vet['Nombre_Sucursal']) ?></span></td>
                        <td><span class="badge <?= $vet['Estado'] == 'Activo' ? 'bg-success' : 'bg-danger' ?>"><?= $vet['Estado'] ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary btn-editar" 
                                data-idusu="<?= $vet['ID_Usuario'] ?>"
                                data-nombre="<?= htmlspecialchars($vet['Nombre']) ?>"
                                data-apellido="<?= htmlspecialchars($vet['Apellido']) ?>"
                                data-estado="<?= $vet['Estado'] ?>"
                                data-bs-toggle="modal" data-bs-target="#modalEditar">
                                <i class="fas fa-cog"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalRegistroVet" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form id="formRegistroVet" action="../../controllers/admin/VeterinarioController.php?action=registrar" method="POST">
                    <div class="modal-header text-white" style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-id-card-alt me-2"></i> Registro Profesional de Salud</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-4 border-end px-4">
                                <div class="section-header">1. Credenciales de Acceso</div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Correo Electrónico</label>
                                    <input type="email" name="correo" id="email_v" class="form-control" placeholder="medico@docuhuella.com" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_email" class="error-msg text-danger d-none">Formato de correo inválido</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirmar Correo</label>
                                    <input type="email" id="email_conf" name="confirmar_correo" class="form-control" placeholder="Repite el correo" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_email_conf" class="error-msg text-danger d-none">Los correos no coinciden</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contraseña Temporal</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background-color: var(--dh-light-gray); border-top-left-radius: 20px; border-bottom-left-radius: 20px; padding-left: 20px;">
                                            <i class="fas fa-lock" style="color: #1a1a1a;"></i>
                                        </span>
                                        <input type="password" class="form-control" id="pass_v" name="contrasena" required placeholder="••••••••" style="background-color: var(--dh-light-gray); padding-left: 10px;">
                                        <button class="btn d-flex align-items-center justify-content-center" type="button" onclick="togglePassword('pass_v', 'linea-v')" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); padding: 0 20px;">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 13 Q 12 5 19 13" />
                                                <circle cx="12" cy="14" r="2.5" />
                                                <line id="linea-v" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background-color: var(--dh-light-gray); border-top-left-radius: 20px; border-bottom-left-radius: 20px; padding-left: 20px;">
                                            <i class="fas fa-lock" style="color: #1a1a1a;"></i>
                                        </span>
                                        <input type="password" class="form-control" id="pass_conf" name="confirmar_contrasena" required placeholder="••••••••" style="background-color: var(--dh-light-gray); padding-left: 10px;">
                                        <button class="btn d-flex align-items-center justify-content-center" type="button" onclick="togglePassword('pass_conf', 'linea-conf')" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: var(--dh-light-gray); padding: 0 20px;">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 13 Q 12 5 19 13" />
                                                <circle cx="12" cy="14" r="2.5" />
                                                <line id="linea-conf" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                            </svg>
                                        </button>
                                    </div>
                                    <small id="err_pass_conf" class="error-msg text-danger d-none">Las contraseñas no coinciden</small>
                                </div>
                            </div>

                            <div class="col-lg-4 border-end px-4">
                                <div class="section-header">2. Información Personal</div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6"><label class="form-label fw-semibold">Nombre</label><input type="text" name="nombre" class="form-control" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;"></div>
                                    <div class="col-6"><label class="form-label fw-semibold">Apellido</label><input type="text" name="apellido" class="form-control" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Cédula de Identidad</label>
                                    <input type="text" name="cedula" id="cedula_v" class="form-control mascara-cedula" placeholder="000-0000000-0" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_cedula" class="error-msg text-danger d-none">Cédula incompleta (11 dígitos)</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Número de Teléfono</label>
                                    <input type="text" name="telefono" id="tel_v" class="form-control mascara-telefono" placeholder="809-000-0000" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_tel" class="error-msg text-danger d-none">Teléfono incompleto (10 dígitos)</small>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Género</label>
                                        <select name="sexo" class="form-select" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                            <option value="">...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" id="fecha_n" class="form-control" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                        <small id="err_edad" class="error-msg text-danger d-none">Debe ser mayor de 18 años</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Dirección Residencial </label>
                                    <textarea name="direccion" id="direccion_v" class="form-control" rows="2" 
                                        placeholder="Calle, No. de casa, Sector y Ciudad..." required
                                        style="background-color: var(--dh-light-gray); border-radius: 15px; padding: 10px 20px; resize: none;"></textarea>
                                    <div class="invalid-feedback">Este campo es obligatorio para el registro.</div>
                                </div>
                            </div>

                            <div class="col-lg-4 px-4">
                                <div class="section-header">3. Datos Clínicos</div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Especialidad Médica</label>
                                    <select name="id_especialidad" id="especialidad_v" class="form-select" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                        <option value="">Seleccionar especialidad...</option>
                                        <?php foreach($lista_especialidades as $esp): ?>
                                            <option value="<?= $esp['ID_Especialidad'] ?>">
                                                <?= htmlspecialchars($esp['Nombre_Especialidad']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.7rem; padding-left: 10px;">
                                        Si no aparece la especialidad, contacta a soporte.
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sucursal de Asignación</label>
                                    <select name="id_clinica" class="form-select" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                        <option value="">Seleccionar sucursal...</option>
                                        <?php foreach($mis_sucursales as $s): ?>
                                            <option value="<?= $s['ID_Clinica'] ?>"><?= $s['Nombre_Sucursal'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Exequatur (5 dígitos)</label>
                                    <input type="text" name="exequatur" id="ex_v" class="form-control solo-num" maxlength="5" placeholder="12345" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_ex" class="error-msg text-danger d-none">Debe tener 5 dígitos exactos</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">No. Colegiatura (4 dígitos)</label>
                                    <input type="text" name="colegiatura" id="col_v" class="form-control solo-num" maxlength="4" placeholder="1234" required style="background-color: var(--dh-light-gray); border-radius: 20px; padding: 10px 20px;">
                                    <small id="err_col" class="error-msg text-danger d-none">Debe tener 4 dígitos exactos</small>
                                </div>
                            </div>
                        </div>

                        <div class="security-box mt-4">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-user-shield text-warning me-2"></i> Verificación del Administrador</h6>
                                    <p class="small text-muted mb-0">Confirma que eres el <strong>ADMIN</strong>.</p>
                                    <p class="small text-muted mb-1"> (Recuerda que todos los campos deben estar llenos).</p>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text" style="background-color: white; border-top-left-radius: 20px; border-bottom-left-radius: 20px; padding-left: 20px;">
                                            <i class="fas fa-key" style="color: #f59f00;"></i>
                                        </span>
                                        <input type="password" class="form-control" id="admin_auth" name="contrasena_admin" required placeholder="Contraseña Admin" style="background-color: white; padding-left: 10px;">
                                        <button class="btn d-flex align-items-center justify-content-center" type="button" onclick="togglePassword('admin_auth', 'linea-admin')" style="border-top-right-radius: 20px; border-bottom-right-radius: 20px; background-color: white; padding: 0 20px;">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 13 Q 12 5 19 13" />
                                                <circle cx="12" cy="14" r="2.5" />
                                                <line id="linea-admin" x1="4" y1="4" x2="20" y2="20" style="display: none;" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 20px;">Cancelar</button>
                        <button type="submit" id="btnSubmitVet" class="btn btn-vibrante px-5 shadow fw-bold" disabled>
                            Autorizar y Registrar
                        </button>
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
        // 1. INICIALIZACIÓN DE TABLA
        $(document).ready(function() {
            $('#tablaVets').DataTable({
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando página _PAGE_ de _PAGES_ con un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando página 0 de 0 con un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "search": "Buscar Dr.Veterinario:",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "pageLength": 10,
                "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center"ip>' 
            });
        });

        // 2. UTILIDADES VISUALES (OJO PASSWORD)
        function togglePassword(inputId, lineId) {
            const input = document.getElementById(inputId);
            const line = document.getElementById(lineId);
            if (input.type === "password") {
                input.type = "text";
                line.style.display = "block";
            } else {
                input.type = "password";
                line.style.display = "none";
            }
        }

        // 3. MÁSCARAS DE ENTRADA
        $('.mascara-cedula').on('input', function() {
            let v = $(this).val().replace(/\D/g, '').substring(0,11);
            if (v.length > 10) v = v.replace(/^(\d{3})(\d{7})(\d{1})$/, "$1-$2-$3");
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,7})$/, "$1-$2");
            $(this).val(v);
        });

        $('.mascara-telefono').on('input', function() {
            let v = $(this).val().replace(/\D/g, '').substring(0,10);
            if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{4})$/, "$1-$2-$3");
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})$/, "$1-$2");
            $(this).val(v);
        });

        $('.solo-num').on('input', function() { $(this).val($(this).val().replace(/\D/g, '')); });

        // 4. VALIDACIÓN MAESTRA Y ENVÍO AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formRegistroVet');
            const btn = document.getElementById('btnSubmitVet');

            // --- Validación en tiempo real para habilitar botón ---
            form.addEventListener('input', function() {
                let ok = true;
                form.querySelectorAll('[required]').forEach(i => { if(!i.value.trim()) ok = false; });

                const email = document.getElementById('email_v').value;
                const emailC = document.getElementById('email_conf').value;
                const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                document.getElementById('err_email').classList.toggle('d-none', isEmail || !email);
                document.getElementById('err_email_conf').classList.toggle('d-none', (email === emailC && email !== "") || !emailC);
                if(!isEmail || email !== emailC) ok = false;

                const pass = document.getElementById('pass_v').value;
                const passC = document.getElementById('pass_conf').value;
                document.getElementById('err_pass_conf').classList.toggle('d-none', (pass === passC && pass !== "") || !passC);
                if(pass !== passC) ok = false;

                const ced = document.getElementById('cedula_v').value;
                const tel = document.getElementById('tel_v').value;
                if(ced.length !== 13 || tel.length !== 12) ok = false;
                const direccion = document.getElementById('direccion_v').value.trim();
                if(direccion.length < 3) ok = false;

                const esp = document.getElementById('especialidad_v').value;
                if(!esp) ok = false;
                const ex = document.getElementById('ex_v').value;
                const col = document.getElementById('col_v').value;
                if(ex.length !== 5 || col.length !== 4) ok = false;

                const fecha = document.getElementById('fecha_n').value;
                if(fecha){
                    const hoy = new Date();
                    const cumple = new Date(fecha);
                    let edad = hoy.getFullYear() - cumple.getFullYear();
                    if (hoy.getMonth() < cumple.getMonth() || (hoy.getMonth() === cumple.getMonth() && hoy.getDate() < cumple.getDate())) edad--;
                    document.getElementById('err_edad').classList.toggle('d-none', edad >= 18);
                    if(edad < 18) ok = false;
                } else { ok = false; }

                btn.disabled = !ok;
            });

            // --- Envío del Formulario vía AJAX ---
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Autorizando...';

                fetch('../../controllers/admin/VeterinarioController.php?action=registrar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Si el servidor manda un error de PHP, esto lo atrapará
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (err) {
                            console.log("Respuesta cruda del servidor:", text);
                            throw new Error("El servidor no envió un JSON válido. Revisa la consola.");
                        }
                    });
                })
                .then(data => {
                    if (data.status === 'success') {
                        $('#modalRegistroVet').modal('hide');
                        Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Veterinario registrado.', showConfirmButton: false, timer: 1500 })
                        .then(() => { location.reload(); });
                    } else {
                        
                    let msg = "Error en el registro.";
                        if (data.type === 'auth_admin_fallida') {
                            msg = "Contraseña de administrador incorrecta.";
                        } else if (data.type === 'correo_ya_existe') {
                            msg = "Este correo electrónico ya está registrado por otro usuario.";
                        } else if (data.type === 'cedula_duplicada') {
                            msg = "Error: Esta cédula ya pertenece a otro veterinario en el sistema.";
                        } else if (data.type === 'exequatur_duplicado') {
                            msg = "Error: Este número de Exequátur ya está registrado.";
                        } else if (data.type === 'colegiatura_duplicada') {
                            msg = "Error: El número de colegiatura ya está registrado.";
                        } else if (data.type === 'campos_incompletos') {
                            msg = "Por favor, completa todos los campos requeridos correctamente.";
                        } else if (data.type === 'error_perfil') {
                            msg = "No se pudo crear el perfil médico. Intenta de nuevo.";
                        }
                        
                        Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1A2D40' });
                        btn.disabled = false;
                        btn.innerHTML = 'Autorizar y Registrar';
                        document.getElementById('admin_auth').value = ""; 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({ icon: 'error', title: 'Error Crítico', text: error.message });
                    btn.disabled = false;
                    btn.innerHTML = 'Autorizar y Registrar';
                });
            });
        });
    </script>

</body>
</html>