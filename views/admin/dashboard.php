<?php 
require_once '../../config/auth_check.php';

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
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="../../controllers/admin/DashboardController.php?action=ver" class="active"><i class="fas fa-chart-pie"></i> Mi Resumen</a>
            <a href="../../views/admin/clinicas.php"><i class="fas fa-hospital"></i> Mis Sucursales</a>
            <a href="../../views/admin/registrar_vet.php"><i class="fas fa-user-md"></i> Veterinarios</a>
            <a href="../../views/admin/reportes.php"><i class="fas fa-file-export"></i> Reportes Clinicos</a>
        </nav>
        
        <a href="../../controllers/UsuariosController.php?action=logout" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
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
                <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
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
</body>
</html>