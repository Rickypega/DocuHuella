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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos específicos para el grid de mascotas (inspirado en Mis Notas) */
        .mascota-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: #fff;
            height: 100%;
        }

        .mascota-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }

        .mascota-img-container {
            position: relative;
            width: 100%;
            padding-top: 100%; /* Aspect Ratio 1:1 */
            overflow: hidden;
            background: #f0f2f5;
        }

        .mascota-img-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Garantiza uniformidad sin deformar */
            transition: transform 0.5s ease;
        }

        .mascota-card:hover .mascota-img-container img {
            transform: scale(1.08);
        }

        .mascota-info {
            padding: 18px;
            text-align: center;
        }

        .mascota-nombre {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dh-navy);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mascota-detalle {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .badge-especie {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
            padding: 6px 12px;
            border-radius: 50px;
            background: rgba(255,255,255,0.9);
            color: var(--dh-navy);
            font-weight: 600;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Ajustes para el panel SPA */
        #panel-mis-mascotas {
            animation: fadeIn .3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Encabezado Móvil -->
    <div class="mobile-header d-md-none p-3 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold text-white"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h4>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Menú Lateral -->
    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-success text-white mt-2">Cuidador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/cuidador/dashboard"><i class="fas fa-home"></i> Mi Panel</a>
            <a href="<?= URL_BASE ?>/cuidador/mis-mascotas" class="active" id="enlace-mis-mascotas"><i class="fas fa-bone"></i> Mis Mascotas</a>
            <a href="#" id="enlace-mis-notas" onclick="mostrarPanelNotas(); marcarActivoSidebar(this); return false;">
                <i class="fas fa-sticky-note"></i> Mis Notas
            </a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2 mx-3" style="border-radius: 10px; padding: 12px;" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger d-flex align-items-center justify-content-center gap-2 mb-4 mx-3" style="border-radius: 10px; padding: 12px;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <!-- Contenedor para ocultar al mostrar notas -->
        <div id="contenido-dashboard">

            <div id="panel-mis-mascotas">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">🐾 Mis Mascotas</h2>
                        <p class="text-muted mb-0">Gestiona y revisa el historial de tus compañeros.</p>
                    </div>
                    <button id="btn-registrar-mascota" class="btn btn-primary px-4 py-2" type="button" data-bs-toggle="modal" data-bs-target="#modalRegistrarMascota" style="border-radius: 12px; background-color: var(--dh-navy); border: none;">
                        <i class="fas fa-plus me-2"></i>Registrar Mascota
                    </button>
                </div>

                <?php if (empty($mascotas)): ?>
                    <div class="text-center py-5 shadow-sm bg-white" style="border-radius: 20px;">
                        <i class="fas fa-paw fa-5x mb-4" style="color: #dee2e6;"></i>
                        <h5 class="text-muted">Aún no tienes mascotas registradas.</h5>
                        <p class="text-muted">Cuando tú o un veterinario registren a tu mascota, aparecerá aquí.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($mascotas as $m): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="mascota-card" onclick="location.href='<?= URL_BASE ?>/cuidador/mascota/ver?id=<?= $m['ID_Mascota'] ?>'">
                                    <div class="mascota-img-container">
                                        <span class="badge-especie"><?= htmlspecialchars($m['Especie']) ?></span>
                                        <?php 
                                            $foto_path = 'public/images/default_pet.png'; // Fallback
                                            $id = $m['ID_Mascota'];
                                            // Buscar posibles extensiones
                                            if (file_exists(APP_PATH . "/public/uploads/pets/$id.jpg")) $foto_path = "public/uploads/pets/$id.jpg";
                                            elseif (file_exists(APP_PATH . "/public/uploads/pets/$id.jpeg")) $foto_path = "public/uploads/pets/$id.jpeg";
                                            elseif (file_exists(APP_PATH . "/public/uploads/pets/$id.png")) $foto_path = "public/uploads/pets/$id.png";
                                        ?>
                                        <img src="<?= URL_BASE ?>/<?= $foto_path ?>" alt="<?= htmlspecialchars($m['Nombre']) ?>">
                                    </div>
                                    <div class="mascota-info">
                                        <h5 class="mascota-nombre"><?= htmlspecialchars($m['Nombre']) ?></h5>
                                        <p class="mascota-detalle"><?= htmlspecialchars($m['Raza']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /#contenido-dashboard -->

        <!-- Panel de Notas (SPA) -->
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>
        <?php include_once APP_PATH . '/views/includes/modal_registrar_mascota.php'; ?>

    </div>


    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
    
    <script>
        function marcarActivoSidebar(el) {
            document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
            el.classList.add('active');
        }

        // Si hay una alerta de seguridad por URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'acceso_denegado') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso Denegado',
                    text: 'No tienes permiso para ver esta mascota.',
                    confirmButtonColor: '#1A2D40'
                });
            } else {
                alert('Acceso Denegado: No tienes permiso para ver esta mascota.');
            }
        }
    </script>
</body>
</html>
