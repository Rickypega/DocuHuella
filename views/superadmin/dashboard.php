<?php 
if(!isset($total_clinicas)) {
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

        body {
            background-color: var(--dh-light-gray);
            overflow-x: hidden;
        }

        /* Diseño del Sidebar (Menú Lateral) */
        .sidebar {
            height: 100vh;
            background-color: var(--dh-navy);
            color: white;
            position: fixed;
            width: 260px;
            top: 0;
            left: 0;
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
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(234, 218, 193, 0.1); 
            color: var(--dh-beige);
            border-left: 4px solid var(--dh-beige);
        }

        .sidebar i {
            margin-right: 10px;
            width: 25px;
            text-align: center;
        }

        /* Botón especial de Cerrar Sesión (ROJO) */
        .sidebar .btn-logout {
            background-color: #dc3545; 
            color: white !important; 
            margin: 0 15px;
            border-radius: 10px;
            text-align: center;
            padding: 12px 15px;
            font-weight: bold;
        }

        .sidebar .btn-logout:hover {
            background-color: #c82333; 
            border-left: none; 
            transform: scale(1.02); 
        }

        /* Contenido Principal */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Tarjetas de Estadísticas */
        .stat-card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            border-left: 5px solid var(--dh-navy);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--dh-beige);
            opacity: 0.8;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--dh-navy);
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">
                <?php echo isset($_SESSION['nombre_rol']) ? htmlspecialchars($_SESSION['nombre_rol']) : 'SuperAdmin'; ?>
            </span>
        </div>

        <a href="#" class="active"><i class="fas fa-chart-pie"></i> Estadísticas</a>
        <a href="#"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
        <a href="#"><i class="fas fa-file-export"></i> Gestión de Reportes</a>
        
        <div style="position: absolute; bottom: 20px; width: 100%;">
            <a href="../login.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--dh-navy);">Visión General del Sistema</h2>
           <div class="user-profile text-muted">
                <span>Bienvenido Sr.<strong><?php echo htmlspecialchars($nombre_rol); ?></strong></span>
                <i class="fas fa-user-circle fs-4 ms-2 align-middle"></i>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Clínicas (Admins)</div>
                            <div class="stat-number"><?php echo number_format($total_clinicas); ?></div>
                        </div>
                        <i class="fas fa-hospital stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card" style="border-left-color: var(--dh-beige);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Expedientes Creados</div>
                            <div class="stat-number"><?php echo number_format($total_expedientes); ?></div>
                        </div>
                        <i class="fas fa-folder-open stat-icon" style="color: var(--dh-navy);"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Veterinarios Activos</div>
                            <div class="stat-number"><?php echo number_format($total_veterinarios); ?></div>
                        </div>
                        <i class="fas fa-user-md stat-icon"></i>
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
                        <h5 class="fw-bold mb-4" style="color: var(--dh-navy);">Distribución General del Sistema</h5>
                        <div style="height: 300px;">
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
                type: 'bar', // Tipo de gráfico: barras
                data: {
                    labels: ['Clínicas (Admins)', 'Veterinarios', 'Cuidadores', 'Mascotas', 'Expedientes'],
                    datasets: [{
                        label: 'Total en el Sistema',
                        // ¡Aquí inyectamos los datos reales desde PHP!
                        data: [
                            <?php echo $total_clinicas; ?>,
                            <?php echo $total_veterinarios; ?>,
                            <?php echo $total_cuidadores; ?>,
                            <?php echo $total_mascotas; ?>,
                            <?php echo $total_expedientes; ?>
                        ],
                        // Usamos la paleta de colores de DocuHuella alternada
                        backgroundColor: [
                            '#1A2D40', // Navy
                            '#EADAC1', // Beige
                            '#1A2D40', // Navy
                            '#EADAC1', // Beige
                            '#1A2D40'  // Navy
                        ],
                        borderRadius: 8, // Bordes redondeados en las barras
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Ocultamos la leyenda extra para que se vea más limpio
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // Evita que salgan decimales (no hay "media" mascota)
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>