<?php 
require_once __DIR__ . '/../../config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
// Si alguien teclea "dashboard.php" en la URL, lo mandamos a su controlador respectivo.
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: ../../controllers/admin/DashboardController.php?action=ver");
    exit();
}
// Si llegamos aquí, es porque el controlador nos cargó correctamente mediante "require"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/admin/dashboard" class="active"><i class="fas fa-chart-pie"></i> Mi Resumen</a>
            <a href="<?= URL_BASE ?>/views/admin/clinicas.php"><i class="fas fa-hospital"></i> Mis Sucursales</a>
            <a href="<?= URL_BASE ?>/views/admin/registrar_vet.php"><i class="fas fa-user-md"></i> Veterinarios</a>
            <a href="<?= URL_BASE ?>/views/admin/reportes.php"><i class="fas fa-file-medical-alt"></i> Reportes Clinicos</a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2" style="border-radius: 10px; padding: 12px; margin: 0 15px; border-color: rgba(255,255,255,0.2);" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
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
        
        <?php if (isset($_GET['exito']) && $_GET['exito'] == 'vet_registrado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Excelente!</strong> El veterinario ha sido registrado y asignado a la sucursal correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-2">
            <div class="user-profile text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end mb-4 pb-2 border-bottom flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-0" style="color: var(--dh-navy);"><?php echo htmlspecialchars($titulo_dashboard); ?></h2>
                <p class="text-muted mt-1">Estado de tus sucursales y personal</p>
            </div>

            <form method="GET" action="../../controllers/admin/DashboardController.php" class="d-flex gap-2 align-items-end bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="action" value="ver">
                
                <div>
                    <label class="form-label text-muted" style="font-size: 0.8rem; margin-bottom: 2px;">Sucursal</label>
                    <select name="id_clinica" class="form-select form-select-sm" style="min-width: 150px;">
                        <option value="">Todas las Clínicas</option>
                        <?php foreach($mis_clinicas as $clinica): ?>
                            <option value="<?php echo $clinica['ID_Clinica']; ?>" 
                                <?php echo (isset($_GET['id_clinica']) && $_GET['id_clinica'] == $clinica['ID_Clinica']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($clinica['Nombre_Sucursal']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label text-muted" style="font-size: 0.8rem; margin-bottom: 2px;">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" 
                           value="<?php echo isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : ''; ?>">
                </div>

                <div>
                    <label class="form-label text-muted" style="font-size: 0.8rem; margin-bottom: 2px;">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" 
                           value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : ''; ?>">
                </div>

                <button type="submit" class="btn btn-sm text-white" style="background-color: #0060ef;">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                
                <?php if(!empty($_GET['id_clinica']) || !empty($_GET['fecha_inicio'])): ?>
                    <a href="../../controllers/admin/DashboardController.php?action=ver" class="btn btn-sm text-white btn-outline-secondary" style="background-color: #8b0d2c;">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="row g-4">
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #f59f00;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Citas Agendadas</div>
                            <div class="stat-number"><?php echo number_format($total_citas ?? 0); ?></div>
                        </div>
                        <i class="fas fa-calendar-check stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Mis Sucursales</div>
                            <div class="stat-number"><?php echo number_format($total_sucursales ?? 0); ?></div>
                        </div>
                        <i class="fas fa-hospital stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #8a0a2a;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Mis Veterinarios</div>
                            <div class="stat-number"><?php echo number_format($total_veterinarios ?? 0); ?></div>
                        </div>
                        <i class="fas fa-user-md stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Expedientes Clínicos</div>
                            <div class="stat-number"><?php echo number_format($total_expedientes ?? 0); ?></div>
                        </div>
                        <i class="fas fa-folder-open stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Clientes / Cuidadores</div>
                            <div class="stat-number"><?php echo number_format($total_cuidadores ?? 0); ?></div>
                        </div>
                        <i class="fas fa-users stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card" style="border-left-color: #05ac37;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Mascotas Atendidas</div>
                            <div class="stat-number"><?php echo number_format($total_mascotas ?? 0); ?></div>
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
                        <h5 class="fw-bold mb-4" style="color: var(--dh-navy);">Rendimiento Operativo</h5>
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
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('graficoSistema').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    // Actualizado para reflejar las nuevas métricas del Admin
                    labels: ['Citas', 'Sucursales', 'Veterinarios', 'Clientes', 'Mascotas', 'Expedientes'],
                    datasets: [{
                        label: 'Volumen de Datos',
                        data: [
                            <?php echo $total_citas ?? 0; ?>,
                            <?php echo $total_sucursales ?? 0; ?>,
                            <?php echo $total_veterinarios ?? 0; ?>,
                            <?php echo $total_cuidadores ?? 0; ?>,
                            <?php echo $total_mascotas ?? 0; ?>,
                            <?php echo $total_expedientes ?? 0; ?>
                        ],
                        backgroundColor: ['#f59f00', '#0d6efd', '#8a0a2a', '#1A2D40', '#05ac37', '#1A2D40'],
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