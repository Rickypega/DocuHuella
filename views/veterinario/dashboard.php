<?php 
require_once '../../config/auth_check.php';

// 🔒 Evitar acceso directo
//if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    //header("Location: ../../controllers/veterinario/DashboardController.php?action=ver");
    //exit();
//}

// 🔹 DATOS ESTÁTICOS PARA PRUEBA
$nombre_usuario = "Luis Cuevas";

$total_mascotas = 128;
$total_citas_hoy = 12;
$total_citas_pendientes = 7;
$total_clientes = 54;
$total_expedientes = 89;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Veterinario - DocuHuella</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --dh-beige: #EADAC1;
            --dh-navy: #1A2D40;
            --dh-light-gray: #F8F9FA;
        }

        body {
            background-color: var(--dh-light-gray);
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            height: 100vh;
            background-color: var(--dh-navy);
            color: white;
            position: fixed;
            width: 260px;
            display: flex;
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

        .sidebar i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white !important;
            margin: auto 15px 20px;
            border-radius: 10px;
            text-align: center;
            padding: 12px;
            font-weight: bold;
            border: none;
        }

        .btn-logout:hover {
            background-color: #c82333;
            transform: scale(1.02);
        }

        /* MAIN */
        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        /* CARDS */
        .stat-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid var(--dh-navy);
            transition: 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            font-size: 2.2rem;
            color: var(--dh-beige);
            opacity: 0.8;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dh-navy);
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo-container">
        <h3 class="fw-bold text-white mb-0">
            <i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella
        </h3>
        <span class="badge" style="background-color: var(--dh-beige); color: #000;">
            Veterinario
        </span>
    </div>

    <nav class="mt-3">
        <a href="#" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="#"><i class="fas fa-calendar-check"></i> Citas</a>
        <a href="#"><i class="fas fa-dog"></i> Mascotas</a>
        <a href="#"><i class="fas fa-users"></i> Clientes</a>
        <a href="#"><i class="fas fa-file-medical"></i> Expedientes</a>
    </nav>

    <a href="#" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </a>
</div>

<!-- MAIN -->
<div class="main-content">

    <!-- PERFIL -->
    <div class="d-flex justify-content-end mb-2">
        <div class="user-profile text-muted d-flex align-items-center">
            <span>
                Bienvenido Dr(a). <strong><?php echo $nombre_usuario; ?></strong>
            </span>
            <i class="fas fa-user-md fs-3 ms-2 text-secondary"></i>
        </div>
    </div>

    <!-- HEADER -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Panel Veterinario</h2>
        <p class="text-muted mt-1">Gestión general de la clínica</p>
    </div>

    <!-- CARDS -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Mascotas</div>
                        <div class="stat-number"><?php echo $total_mascotas; ?></div>
                    </div>
                    <i class="fas fa-dog stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Citas Hoy</div>
                        <div class="stat-number"><?php echo $total_citas_hoy; ?></div>
                    </div>
                    <i class="fas fa-calendar-day stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Citas Pendientes</div>
                        <div class="stat-number"><?php echo $total_citas_pendientes; ?></div>
                    </div>
                    <i class="fas fa-clock stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Clientes</div>
                        <div class="stat-number"><?php echo $total_clientes; ?></div>
                    </div>
                    <i class="fas fa-users stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Expedientes Clínicos</div>
                        <div class="stat-number"><?php echo $total_expedientes; ?></div>
                    </div>
                    <i class="fas fa-folder-open stat-icon"></i>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>