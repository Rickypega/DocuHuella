<?php
require_once 'config/auth_check.php';
// SEGURIDAD: Evitar acceso directo a la vista. 
// Si alguien teclea "dashboard.php" en la URL, lo mandamos al controlador.
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/superadmin/dashboard");
    exit();
}
// Si llegamos aquí, es porque el controlador nos cargó correctamente mediante "require"
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
            <span class="badge bg-warning text-dark mt-2">Super Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/superadmin/dashboard" class="active"><i class="fas fa-chart-pie"></i>
                Estadísticas</a>
            <a href="<?= URL_BASE ?>/views/superadmin/administrador.php"><i class="fas fa-hospital"></i> Gestión de
                Clínicas</a>
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
            <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Visión General</h2>
            <p class="text-muted mt-1">Estado global de la plataforma DocuHuella</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Administradores</div>
                            <div class="stat-number"><?php echo number_format($total_admins); ?></div>
                        </div>
                        <i class="fas fa-user-tie stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Clínicas / Sedes</div>
                            <div class="stat-number"><?php echo number_format($total_clinicas); ?></div>
                        </div>
                        <i class="fas fa-hospital stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #8a0a2a;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Veterinarios</div>
                            <div class="stat-number"><?php echo number_format($total_veterinarios); ?></div>
                        </div>
                        <i class="fas fa-user-md stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Expedientes</div>
                            <div class="stat-number"><?php echo number_format($total_expedientes); ?></div>
                        </div>
                        <i class="fas fa-folder-open stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Cuidadores Registrados</div>
                            <div class="stat-number"><?php echo number_format($total_cuidadores); ?></div>
                        </div>
                        <i class="fas fa-users stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card" style="border-left-color: #05ac37;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Mascotas en Sistema</div>
                            <div class="stat-number"><?php echo number_format($total_mascotas); ?></div>
                        </div>
                        <i class="fas fa-dog stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: var(--dh-navy);">Distribución del Sistema</h5>
                        <div style="height: 350px;">
                            <canvas id="graficoSistema"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('graficoSistema').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Admins', 'Clínicas', 'Veterinarios', 'Cuidadores', 'Mascotas', 'Expedientes'],
                    datasets: [{
                        label: 'Registros Totales',
                        data: [
                            <?php echo $total_admins; ?>,
                            <?php echo $total_clinicas; ?>,
                            <?php echo $total_veterinarios; ?>,
                            <?php echo $total_cuidadores; ?>,
                            <?php echo $total_mascotas; ?>,
                            <?php echo $total_expedientes; ?>
                        ],
                        backgroundColor: ['#0d6efd', '#1A2D40', '#8a0a2a', '#1A2D40', '#05ac37', '#1A2D40'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>

</html>