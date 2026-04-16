<?php
require_once 'config/auth_check.php';
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/superadmin/catalogos");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella - Catálogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        .catalog-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .nav-pills-custom .nav-link {
            color: var(--dh-navy);
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 20px;
            transition: all 0.3s;
            background: #f8f9fa;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-pills-custom .nav-link.active {
            background-color: var(--dh-navy) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(26, 45, 64, 0.2);
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

        @media (max-width: 768px) {
            .nav-pills-custom {
                flex-direction: row !important;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            .nav-pills-custom .nav-link {
                white-space: nowrap;
            }
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
            <span class="badge bg-warning text-dark mt-2">Super Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/superadmin/dashboard"><i class="fas fa-chart-pie"></i> Estadísticas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/administrador.php"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/reportes.php"><i class="fas fa-file-export"></i> Gestión de Reportes</a>
            <a href="<?= URL_BASE ?>/superadmin/catalogos" class="active"><i class="fas fa-tags"></i> Gestión de Catálogos</a>
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
                <span>Bienvenido Sr. <strong>Super Admin</strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <div id="contenido-dashboard">
            <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Configuración de Catálogos</h2>
                    <p class="text-muted mt-1">Administra los valores globales del sistema</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Columna de Navegación (Pills) -->
                <div class="col-lg-3">
                    <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-vacunas-tab" data-bs-toggle="pill" data-bs-target="#v-pills-vacunas" type="button" role="tab">
                            <i class="fas fa-syringe"></i> Vacunas
                        </button>
                        <button class="nav-link" id="v-pills-especies-tab" data-bs-toggle="pill" data-bs-target="#v-pills-especies" type="button" role="tab">
                            <i class="fas fa-dna"></i> Especies
                        </button>
                        <button class="nav-link" id="v-pills-razas-tab" data-bs-toggle="pill" data-bs-target="#v-pills-razas" type="button" role="tab">
                            <i class="fas fa-tag"></i> Razas
                        </button>
                        <button class="nav-link" id="v-pills-colores-tab" data-bs-toggle="pill" data-bs-target="#v-pills-colores" type="button" role="tab">
                            <i class="fas fa-palette"></i> Colores
                        </button>
                        <button class="nav-link" id="v-pills-especialidades-tab" data-bs-toggle="pill" data-bs-target="#v-pills-especialidades" type="button" role="tab">
                            <i class="fas fa-user-md"></i> Especialidades
                        </button>
                    </div>
                </div>

                <!-- Columna de Contenido -->
                <div class="col-lg-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- CATÁLOGO VACUNAS -->
                        <div class="tab-pane fade show active" id="v-pills-vacunas" role="tabpanel">
                            <div class="catalog-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-syringe me-2 text-primary"></i>Vacunas</h5>
                                    <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="abrirModalCatalogo('vacunas')">
                                        <i class="fas fa-plus me-1"></i> Nueva Vacuna
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover datatable-custom">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Refuerzo (Meses)</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vacunas as $v): ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($v['Nombre_Vacuna']) ?></td>
                                                    <td><?= $v['Periodo_Refuerzo_Meses'] ?> meses</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-warning btn-action me-1" onclick="editarCatalogo('vacunas', <?= htmlspecialchars(json_encode($v)) ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-action" onclick="eliminarCatalogo('vacunas', <?= $v['ID_Vacuna'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CATÁLOGO ESPECIES -->
                        <div class="tab-pane fade" id="v-pills-especies" role="tabpanel">
                            <div class="catalog-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-dna me-2 text-success"></i>Especies</h5>
                                    <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="abrirModalCatalogo('especies')">
                                        <i class="fas fa-plus me-1"></i> Nueva Especie
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover datatable-custom">
                                        <thead>
                                            <tr>
                                                <th>Nombre Especie</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($especies as $e): ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($e['Nombre_Especie']) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-warning btn-action me-1" onclick="editarCatalogo('especies', <?= htmlspecialchars(json_encode($e)) ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-action" onclick="eliminarCatalogo('especies', <?= $e['ID_Especie'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CATÁLOGO RAZAS -->
                        <div class="tab-pane fade" id="v-pills-razas" role="tabpanel">
                            <div class="catalog-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-tag me-2 text-info"></i>Razas</h5>
                                    <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="abrirModalCatalogo('razas')">
                                        <i class="fas fa-plus me-1"></i> Nueva Raza
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover datatable-custom">
                                        <thead>
                                            <tr>
                                                <th>Especie</th>
                                                <th>Raza</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($razas as $r): ?>
                                                <tr>
                                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($r['Nombre_Especie']) ?></span></td>
                                                    <td class="fw-bold"><?= htmlspecialchars($r['Nombre_Raza']) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-warning btn-action me-1" onclick="editarCatalogo('razas', <?= htmlspecialchars(json_encode($r)) ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-action" onclick="eliminarCatalogo('razas', <?= $r['ID_Raza'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CATÁLOGO COLORES -->
                        <div class="tab-pane fade" id="v-pills-colores" role="tabpanel">
                            <div class="catalog-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-palette me-2 text-warning"></i>Colores</h5>
                                    <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="abrirModalCatalogo('colores')">
                                        <i class="fas fa-plus me-1"></i> Nuevo Color
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover datatable-custom">
                                        <thead>
                                            <tr>
                                                <th>Nombre Color</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($colores as $c): ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($c['Nombre_Color']) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-warning btn-action me-1" onclick="editarCatalogo('colores', <?= htmlspecialchars(json_encode($c)) ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-action" onclick="eliminarCatalogo('colores', <?= $c['ID_Color'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CATÁLOGO ESPECIALIDADES -->
                        <div class="tab-pane fade" id="v-pills-especialidades" role="tabpanel">
                            <div class="catalog-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-user-md me-2 text-danger"></i>Especialidades</h5>
                                    <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="abrirModalCatalogo('especialidades')">
                                        <i class="fas fa-plus me-1"></i> Nueva Especialidad
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover datatable-custom">
                                        <thead>
                                            <tr>
                                                <th>Nombre Especialidad</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($especialidades as $esp): ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($esp['Nombre_Especialidad']) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-warning btn-action me-1" onclick="editarCatalogo('especialidades', <?= htmlspecialchars(json_encode($esp)) ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-action" onclick="eliminarCatalogo('especialidades', <?= $esp['ID_Especialidad'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>
    </div>

    <!-- MODAL GENÉRICO PARA CATÁLOGOS -->
    <div class="modal fade" id="modalCatalogo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="<?= URL_BASE ?>/superadmin/catalogos/guardar" method="POST">
                    <input type="hidden" name="tipo" id="input_tipo">
                    <input type="hidden" name="id" id="input_id">
                    
                    <div class="modal-header p-4 border-0">
                        <h5 class="modal-title fw-bold" id="modal_titulo">Nuevo Registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body p-4 pt-0">
                        <!-- Campos Dinámicos -->
                        <div id="campos_dinamicos">
                            <!-- El JS inyectará campos aquí según el tipo -->
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Guardar</button>
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
            $('.datatable-custom').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json' },
                order: [[0, 'asc']],
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                stateSave: true // Recordar paginación, filtros y cantidad de registros
            });

            <?php if(isset($_GET['success'])): ?>
                Swal.fire('¡Éxito!', 'La operación se realizó correctamente.', 'success');
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                let msg = 'Ocurrió un error al procesar la solicitud.';
                if('<?= $_GET['error'] ?>' == 'restricted') msg = 'Error: Este registro tiene relaciones activas y no puede ser eliminado (solo editado).';
                Swal.fire('Atención', msg, 'error');
            <?php endif; ?>

            // Activar pestaña según el parámetro 'tab'
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            if (activeTab) {
                const tabButton = document.querySelector(`#v-pills-${activeTab}-tab`);
                if (tabButton) {
                    const tabTrigger = new bootstrap.Tab(tabButton);
                    tabTrigger.show();
                }
            }
        });

        const especies_json = <?= json_encode($especies) ?>;

        function abrirModalCatalogo(tipo) {
            $('#input_tipo').val(tipo);
            $('#input_id').val('');
            $('#modal_titulo').text('Nuevo ' + (tipo.charAt(0).toUpperCase() + tipo.slice(1, -1)));
            generarCampos(tipo);
            $('#modalCatalogo').modal('show');
        }

        function editarCatalogo(tipo, datos) {
            $('#input_tipo').val(tipo);
            
            let id = '';
            if(tipo == 'vacunas') id = datos.ID_Vacuna;
            if(tipo == 'especies') id = datos.ID_Especie;
            if(tipo == 'razas') id = datos.ID_Raza;
            if(tipo == 'colores') id = datos.ID_Color;
            if(tipo == 'especialidades') id = datos.ID_Especialidad;
            
            $('#input_id').val(id);
            $('#modal_titulo').text('Editar ' + (tipo.charAt(0).toUpperCase() + tipo.slice(1, -1)));
            generarCampos(tipo, datos);
            $('#modalCatalogo').modal('show');
        }

        function generarCampos(tipo, datos = null) {
            let html = '';
            
            if(tipo == 'razas') {
                html += `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especie</label>
                        <select name="id_especie" class="form-select border-2" required>
                            <option value="">Seleccione...</option>
                            ${especies_json.map(e => `<option value="${e.ID_Especie}" ${datos && datos.ID_Especie == e.ID_Especie ? 'selected' : ''}>${e.Nombre_Especie}</option>`).join('')}
                        </select>
                    </div>
                `;
            }

            let label = "Nombre";
            let valor_nombre = "";
            
            if(datos) {
                if(tipo == 'vacunas') valor_nombre = datos.Nombre_Vacuna;
                else if(tipo == 'especies') valor_nombre = datos.Nombre_Especie;
                else if(tipo == 'razas') valor_nombre = datos.Nombre_Raza;
                else if(tipo == 'colores') valor_nombre = datos.Nombre_Color;
                else if(tipo == 'especialidades') valor_nombre = datos.Nombre_Especialidad;
            }

            html += `
                <div class="mb-3">
                    <label class="form-label fw-bold">${label}</label>
                    <input type="text" name="nombre" class="form-control border-2" required value="${valor_nombre}">
                </div>
            `;

            if(tipo == 'vacunas') {
                html += `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control border-2" rows="2">${datos ? datos.Descripcion : ''}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Refuerzo (Meses)</label>
                        <input type="number" name="meses" class="form-control border-2" value="${datos ? datos.Periodo_Refuerzo_Meses : '0'}" min="0">
                    </div>
                `;
            }

            $('#campos_dinamicos').html(html);
        }

        function eliminarCatalogo(tipo, id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el registro de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= URL_BASE ?>/superadmin/catalogos/eliminar?tipo=${tipo}&id=${id}`;
                }
            })
        }

        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
