<?php 
// Si no hay datos, es porque no pasó por el controlador
if(!isset($total_admins)) {
    header("Location: ../../controllers/superadmin/DashboardController.php?action=ver");
    exit();
}
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

        body { background-color: var(--dh-light-gray); overflow-x: hidden; }

        .sidebar {
            height: 100vh;
            background-color: var(--dh-navy);
            color: white;
            position: fixed;
            width: 260px;
            top: 0; left: 0;
            padding-top: 20px;
        }

        .sidebar .logo-container {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(234, 218, 193, 0.1); 
            color: var(--dh-beige);
            border-left: 4px solid var(--dh-beige);
        }

        .sidebar .btn-logout {
            background-color: #dc3545; 
            color: white !important; 
            margin: 0 15px;
            border-radius: 10px;
            text-align: center;
            padding: 12px 15px;
            font-weight: bold;
        }

        .main-content { margin-left: 260px; padding: 30px; }

        .stat-card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid var(--dh-navy);
            transition: 0.2s;
        }

        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 2.2rem; color: var(--dh-beige); opacity: 0.8; }
        .stat-number { font-size: 1.8rem; font-weight: bold; color: var(--dh-navy); }
        .stat-label { color: #6c757d; font-weight: 500; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0">
                <i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella
            </h3>
            <span class="badge bg-warning text-dark mt-2">Super Admin</span>
        </div>

        <a href="#" class="active"><i class="fas fa-chart-pie"></i> Estadísticas</a>
        <a href="administradores.php"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
        <a href="#"><i class="fas fa-file-export"></i> Gestión de Reportes</a>
        
        <div style="position: absolute; bottom: 20px; width: 100%;">
            <a href="../../controllers/UsuariosController.php?action=logout" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--dh-navy);">Visión General del Sistema</h2>
            <div class="user-profile text-muted">
                <span>Bienvenido, <strong><?php echo htmlspecialchars($nombre_rol); ?></strong></span>
                <i class="fas fa-user-circle fs-4 ms-2 align-middle"></i>
            </div>
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
                            <div class="stat-label">Cuidadores (Clientes Finales)</div>
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
                            <div class="stat-label">Mascotas Registradas</div>
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
                        <h5 class="fw-bold mb-4" style="color: var(--dh-navy);">Crecimiento Global del Sistema</h5>
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
                        label: 'Total Registrado',
                        data: [
                            <?php echo $total_admins; ?>,
                            <?php echo $total_clinicas; ?>,
                            <?php echo $total_veterinarios; ?>,
                            <?php echo $total_cuidadores; ?>,
                            <?php echo $total_mascotas; ?>,
                            <?php echo $total_expedientes; ?>
                        ],
                        backgroundColor: ['#0d6efd', '#1A2D40', '#c5aa7f', '#1A2D40', '#c5aa7f', '#1A2D40'],
                        borderRadius: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
</body>
</html>