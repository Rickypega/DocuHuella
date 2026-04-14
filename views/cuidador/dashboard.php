<?php 
require_once APP_PATH . '/config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/cuidador/dashboard");
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
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-success text-white mt-2">Cuidador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/cuidador/dashboard" class="active"><i class="fas fa-home"></i> Mi Panel</a>
            <a href="<?= URL_BASE ?>/views/cuidador/mis_mascotas.php"><i class="fas fa-bone"></i> Mis Mascotas</a>
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
                <span><?php echo (isset($_SESSION['sexo']) && $_SESSION['sexo'] == 'F') ? 'Bienvenida' : 'Bienvenido'; ?>, <strong><?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Cuidador'; ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <div class="mb-4 pb-2 border-bottom">
            <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Mi Espacio DocuHuella</h2>
            <p class="text-muted mt-1">Sigue el estado de tus queridas mascotas</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Mascotas a mi Cargo</div>
                            <div class="stat-number"><?php echo number_format($total_mascotas ?? 0); ?></div>
                        </div>
                        <i class="fas fa-paw stat-icon" style="color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>
</html>