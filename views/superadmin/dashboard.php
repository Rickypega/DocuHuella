<?php 
// SEGURIDAD: Evitar acceso directo a la vista. 
// Si alguien teclea "dashboard.php" en la URL, lo mandamos al controlador.
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: ../../controllers/superadmin/DashboardController.php?action=ver");
    exit();
}
// Si llegamos aquí, es porque el controlador nos cargó correctamente mediante "require"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel SuperAdmin - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dh-beige: #c5aa7f;
            --dh-navy: #1A2D40;
            --dh-light-gray: #F8F9FA;
        }

        body { background-color: var(--dh-light-gray); overflow-x: hidden; font-family: 'Segoe UI', Tahoma, sans-serif; }

        /* Diseño del Sidebar Restaurado */
        .sidebar {
            height: 100vh;
            background-color: var(--dh-navy);
            color: white;
            position: fixed;
            width: 260px;
            display: flex; /* Flexbox para empujar el botón abajo */
            flex-direction: column;
        }

        .sidebar .logo-container {
            text-align: center;
            padding: 25px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(234, 218, 193, 0.1); 
            color: var(--dh-beige);
            border-left: 4px solid var(--dh-beige);
        }

        .sidebar i { width: 25px; text-align: center; margin-right: 10px; }

        /* Botón de Logout Restaurado */
        .btn-logout {
            background-color: #dc3545; 
            color: white !important; 
            margin: auto 15px 20px; /* El 'auto' empuja el botón hasta abajo */
            border-radius: 10px;
            text-align: center;
            padding: 12px;
            font-weight: bold;
            transition: 0.3s;
            border: none;
        }

        .btn-logout:hover { background-color: #c82333; transform: scale(1.02); }

        /* Contenido Principal */
        .main-content { margin-left: 260px; padding: 40px; }

        /* Tarjetas de Estadísticas */
        .stat-card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid var(--dh-navy);
            transition: 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .stat-icon { font-size: 2.2rem; color: var(--dh-beige); opacity: 0.8; }
        .stat-number { font-size: 1.8rem; font-weight: bold; color: var(--dh-navy); }
        .stat-label { color: #6c757d; font-weight: 500; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">Super Admin</span>
        </div>

        <nav class="mt-3">
            <a href="../../controllers/superadmin/DashboardController.php?action=ver" class="active"><i class="fas fa-chart-pie"></i> Estadísticas</a>
            <a href="../../views/superadmin/administrador.php"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
            <a href="../../views/superadmin/reportes.php"><i class="fas fa-file-export"></i> Gestión de Reportes</a>
        </nav>
        
        <a href="../../controllers/UsuariosController.php?action=logout" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-end mb-2">
            <div class="user-profile text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong><?php echo htmlspecialchars($nombre_rol); ?></strong></span>
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
                <div class="stat-card" style="border-left-color: var(--dh-beige);">
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
                <div class="stat-card">
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
        document.addEventListener("DOMContentLoaded", function() {
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
                        backgroundColor: ['#0d6efd', '#1A2D40', '#c5aa7f', '#1A2D40', '#c5aa7f', '#1A2D40'],
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
</body>
</html>