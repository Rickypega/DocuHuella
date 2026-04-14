<?php 
require_once '../../config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista sin sesión. 
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../login.php?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';
$database = new Database();
$db = $database->getConnection();
$sucursales = []; // Placeholder si se ocupa poblar.

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
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">Administrador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/admin/dashboard"><i class="fas fa-chart-pie"></i> Mi Resumen</a>
            <a href="<?= URL_BASE ?>/views/admin/clinicas.php" class="active"><i class="fas fa-hospital"></i> Mis Sucursales</a>
            <a href="<?= URL_BASE ?>/views/admin/registrar_vet.php"><i class="fas fa-user-md"></i> Veterinarios</a>
            <a href="<?= URL_BASE ?>/views/admin/reportes.php"><i class="fas fa-file-medical-alt"></i> Reportes Clinicos</a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2" style="border-radius: 10px; padding: 12px; margin: 0 15px; border-color: rgba(255,255,255,0.2);" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; padding: 12px; margin: 0 15px 20px; width: auto !important;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-end mb-2">
            <div class="user-profile text-muted d-flex align-items-center">
                <span>Bienvenido Sr. <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Listado de Sucursales</h2>
                <p class="text-muted mt-1">Gestión de tus ubicaciones clínicas</p>
            </div>
            
            <a href="<?= URL_BASE ?>/views/admin/crear_sucursal.php" class="btn text-white" style="background-color: var(--dh-navy);">
                <i class="fas fa-plus"></i> Crear Nueva Sucursal
            </a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sucursales)): ?>
                        <?php foreach($sucursales as $s): ?>
                            <tr>
                                <td><?= $s['ID_Clinica'] ?></td>
                                <td><?= $s['Nombre_Sucursal'] ?></td>
                                <td><?= $s['Direccion'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No hay sucursales registradas (o vistas pendientes de conectar al controlador)</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>
</html>
