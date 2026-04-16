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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <style>
        .patient-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eee;
        }
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: none;
        }
        .btn-view {
            background-color: var(--dh-navy);
            color: white;
            border-radius: 8px;
            padding: 5px 15px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background-color: #2c3e50;
            color: white;
            transform: translateY(-2px);
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
            <a href="<?= URL_BASE ?>/veterinario/pacientes" class="active"><i class="fas fa-dog"></i> Gestión de Pacientes</a>
            <a href="<?= URL_BASE ?>/veterinario/consultas"><i class="fas fa-stethoscope"></i> Consultas Médicas</a>
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
            <div class="mb-4 pb-2 border-bottom">
                <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Gestión de Pacientes</h2>
                <p class="text-muted mt-1">Listado de mascotas atendidas y sus responsables</p>
            </div>

        <div class="table-card mt-4">
            <div class="table-responsive">
                <table id="tablaPacientes" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mascota</th>
                            <th>Responsable (Cuidador)</th>
                            <th>Cédula Cuidador</th>
                            <th>Especie / Raza</th>
                            <th>Sexo</th>
                            <th>Última Consulta</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pacientes)): ?>
                            <?php foreach ($pacientes as $p): 
                                $foto_path = URL_BASE . "/public/uploads/pets/" . $p['ID_Mascota'];
                                $foto_real = APP_PATH . "/public/uploads/pets/" . $p['ID_Mascota'];
                                $exts = ['jpg', 'jpeg', 'png'];
                                $foto_url = URL_BASE . "/public/images/mascota_default.png";
                                foreach($exts as $ex) {
                                    if(file_exists($foto_real . "." . $ex)) {
                                        $foto_url = $foto_path . "." . $ex;
                                        break;
                                    }
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $foto_url ?>" alt="<?= htmlspecialchars($p['Nombre']) ?>" class="patient-img">
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($p['Nombre']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-tag text-muted me-1"></i>
                                        <?= htmlspecialchars($p['Nombre_Cuidador'] . " " . $p['Apellido_Cuidador']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <?= htmlspecialchars($p['Cedula_Cuidador']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($p['Especie'] ?? 'S/E') ?>
                                        </span>
                                        <small class="text-muted d-block"><?= htmlspecialchars($p['Raza'] ?? 'Meztizo') ?></small>
                                    </td>
                                    <td>
                                        <i class="fas <?= $p['Sexo'] === 'M' ? 'fa-mars text-primary' : 'fa-venus text-danger' ?>"></i>
                                        <?= $p['Sexo'] === 'M' ? 'Macho' : 'Hembra' ?>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 0.9rem;">
                                            <?= $p['Ultima_Consulta'] ? date('d/m/Y', strtotime($p['Ultima_Consulta'])) : 'Sin registro' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= URL_BASE ?>/veterinario/paciente/ver?id=<?= $p['ID_Mascota'] ?>" class="btn btn-view">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>

    <script>
        $(document).ready(function() {
            $('#tablaPacientes').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                },
                order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                responsive: true
            });
        });

        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
