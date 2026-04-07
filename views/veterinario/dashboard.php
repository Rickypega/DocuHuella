<?php 
require_once '../../config/auth_check.php';
require_once '../../config/db.php';

// SEGURIDAD: Solo Veterinarios (Rol 2)
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../login.php?error=acceso_denegado"); exit();
}

$database = new Database();
$db = $database->getConnection();

// Aquí irían las consultas para los contadores (Pacientes, Citas hoy, etc.)
// Por ahora usaremos valores de ejemplo para la vista.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Veterinario - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --dh-beige: #c5aa7f;
            --dh-navy: #1A2D40; 
            --dh-light-gray: #F1F3F5; 
        }
        
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }

        /* SIDEBAR (Misma estructura que el Admin) */
        .sidebar {
            height: 100vh; background-color: var(--dh-navy); color: white;
            position: fixed; width: 260px; display: flex; flex-direction: column; z-index: 1000;
        }
        .sidebar .logo-container {
            text-align: center; padding: 25px 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; color: rgba(255, 255, 255, 0.7);
            display: block; transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(234, 218, 193, 0.1); color: var(--dh-beige);
            border-left: 4px solid var(--dh-beige);
        }
        .sidebar i { width: 25px; text-align: center; margin-right: 10px; }

        .btn-logout {
            background-color: #dc3545; color: white !important; 
            margin: auto 15px 20px; border-radius: 10px; text-align: center;
            padding: 12px; font-weight: bold; transition: 0.3s; text-decoration: none;
        }
        .btn-logout:hover { background-color: #c82333; transform: scale(1.02); }

        /* CONTENIDO */
        .main-content { margin-left: 260px; padding: 40px; }

        /* TARJETAS DE ESTADÍSTICAS */
        .stat-card {
            border: none; border-radius: 15px; padding: 20px;
            transition: 0.3s; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .icon-box {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }

        .welcome-banner {
            background-color: var(--dh-navy); color: white;
            border-radius: 20px; padding: 30px; margin-bottom: 30px;
            background-image: linear-gradient(45deg, #1A2D40 0%, #2c4a63 100%);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-info text-dark mt-2">Médico Veterinario</span>
        </div>

        <nav class="mt-3">
            <a href="index.php" class="active"><i class="fas fa-th-large"></i> Mi Resumen</a>
            <a href="pacientes.php"><i class="fas fa-dog"></i> Mis Pacientes</a>
            <a href="consultas.php"><i class="fas fa-notes-medical"></i> Consultas</a>
            <a href="agenda.php"><i class="fas fa-calendar-alt"></i> Mi Agenda</a>
        </nav>
        
        <a href="../../controllers/UsuariosController.php?action=logout" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>

    <div class="main-content">
        <div class="welcome-banner d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <h2 class="fw-bold mb-1">¡Hola, Dr. <?= explode(' ', $_SESSION['nombre'])[0] ?>! 👋</h2>
                <p class="mb-0 opacity-75">Aquí tienes un resumen de tu jornada en <strong>DocuHuella</strong>.</p>
            </div>
            <div class="d-none d-md-block">
                <i class="fas fa-user-md fa-4x opacity-25"></i>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">24</h4>
                        <small class="text-muted">Pacientes Activos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">8</h4>
                        <small class="text-muted">Citas para Hoy</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">12</h4>
                        <small class="text-muted">Reportes Pendientes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">0</h4>
                        <small class="text-muted">Urgencias</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark">Próximas Consultas</h5>
                        <a href="agenda.php" class="text-decoration-none small fw-bold" style="color: var(--dh-beige);">Ver toda la agenda</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Motivo</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">09:00 AM</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">🐶</div>
                                            <span>Max (Golden Retriever)</span>
                                        </div>
                                    </td>
                                    <td>Vacunación Anual</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-dark px-3 rounded-pill">Atender</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">10:30 AM</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">🐱</div>
                                            <span>Luna (Siamés)</span>
                                        </div>
                                    </td>
                                    <td>Control Post-operatorio</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-dark px-3 rounded-pill">Atender</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background-color: #fff;">
                    <h5 class="fw-bold mb-4">Acciones Rápidas</h5>
                    
                    <button class="btn btn-outline-dark w-100 mb-3 text-start p-3 rounded-4">
                        <i class="fas fa-plus-circle me-2 text-primary"></i> Nueva Historia Clínica
                    </button>
                    
                    <button class="btn btn-outline-dark w-100 mb-3 text-start p-3 rounded-4">
                        <i class="fas fa-paw me-2 text-success"></i> Registrar Nuevo Paciente
                    </button>

                    <button class="btn btn-outline-dark w-100 text-start p-3 rounded-4">
                        <i class="fas fa-prescription-bottle-med me-2 text-warning"></i> Recetario Digital
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>