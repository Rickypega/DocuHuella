<?php 
require_once APP_PATH . '/config/auth_check.php';

// SEGURIDAD: Evitar acceso directo a la vista. 
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: " . URL_BASE . "/veterinario/dashboard");
    exit();
}

// Convertir las citas a JSON para el calendario
$eventos_calendario = [];
if (!empty($citas)) {
    foreach ($citas as $cita) {
        $eventos_calendario[] = [
            'id'    => $cita['ID_Cita'],
            'title' => '🐾 ' . $cita['Nombre_Mascota'] . ': ' . $cita['Motivo'],
            'start' => $cita['Fecha_Cita'] . 'T' . $cita['Hora_Cita'],
            'extendedProps' => [
                'clinica'     => $cita['Clinica'],
                'estado'      => $cita['Estado']
            ],
            'backgroundColor' => ($cita['Estado'] == 'Pendiente') ? '#f59f00' : ($cita['Estado'] == 'Cancelada' ? '#dc3545' : '#05ac37'),
            'borderColor'     => ($cita['Estado'] == 'Pendiente') ? '#f59f00' : ($cita['Estado'] == 'Cancelada' ? '#dc3545' : '#05ac37')
        ];
    }
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
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css?v=<?= time() ?>">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        .fc {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .fc-toolbar-title {
            color: var(--dh-navy);
            font-weight: 700 !important;
            font-size: 1.25rem !important;
        }
        .fc-button-primary {
            background-color: var(--dh-navy) !important;
            border-color: var(--dh-navy) !important;
        }
        .fc-event {
            cursor: pointer;
            padding: 2px 5px;
            border-radius: 6px;
        }

        /* Ajustes Responsivos para el Calendario */
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 15px;
            }
            .fc-toolbar-title {
                font-size: 1rem !important;
                text-align: center;
            }
            .fc-button {
                padding: 0.4rem 0.65rem !important;
                font-size: 0.85rem !important;
            }
            .fc-header-toolbar {
                margin-bottom: 1.5rem !important;
            }
        }
    </style>
</head>
<body>

    <!-- Encabezado Móvil (Solo visible en pantallas pequeñas) -->
    <div class="mobile-header d-md-none p-3 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold text-white"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h4>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Menú Lateral -->
    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-info text-dark mt-2">Veterinario</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/veterinario/dashboard" class="active" id="enlace-dashboard-vet">
                <i class="fas fa-chart-pie"></i> Mi Panel
            </a>
            <a href="<?= URL_BASE ?>/veterinario/pacientes"><i class="fas fa-dog"></i> Gestión de Pacientes</a>
            <a href="<?= URL_BASE ?>/veterinario/consultas"><i class="fas fa-stethoscope"></i> Consultas Médicas</a>
            <a href="<?= URL_BASE ?>/veterinario/citas"><i class="fas fa-calendar-check"></i> Gestión de Citas</a>
            <a href="<?= URL_BASE ?>/veterinario/vacunas"><i class="fas fa-syringe"></i> Control de Vacunas</a>
            <a href="#" id="enlace-mis-notas"
               onclick="mostrarPanelNotas(); marcarActivoSidebar(this); return false;">
                <i class="fas fa-sticky-note"></i> Mis Notas
            </a>
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
                <span><?php echo (isset($_SESSION['sexo']) && $_SESSION['sexo'] == 'F') ? 'Bienvenida Dra.' : 'Bienvenido Dr.'; ?> <strong><?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Veterinario'; ?></strong></span>
                <i class="fas fa-user-circle fs-3 ms-2 text-secondary"></i>
            </div>
        </div>

        <!-- Contenido principal del dashboard -->
        <div id="contenido-dashboard">
            <div class="mb-4 pb-2 border-bottom">
                <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Visión General - Veterinaria</h2>
                <p class="text-muted mt-1">Resumen de expedientes y consultas clínicas</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="stat-card" style="border-left-color: #05ac37;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Total de Pacientes</div>
                                <div class="stat-number"><?php echo number_format($total_mascotas ?? 0); ?></div>
                            </div>
                            <i class="fas fa-dog stat-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="stat-card" style="border-left-color: #0d6efd;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Consultas Médicas Registradas</div>
                                <div class="stat-number"><?php echo number_format($total_consultas ?? 0); ?></div>
                            </div>
                            <i class="fas fa-notes-medical stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendario de Citas -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4" style="color: var(--dh-navy);">
                                <i class="fas fa-calendar-alt me-2"></i>Mi Agenda de Consultas
                            </h4>
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Mis Notas (oculto por defecto) -->
        <?php include_once APP_PATH . '/views/includes/mis_notas.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales-all.global.min.js'></script>

    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
    
    <script>
    function marcarActivoSidebar(el) {
        document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
        el.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: (window.innerWidth < 768) ? 'listWeek' : 'dayGridMonth',
            locale: 'es',
            height: 'auto',
            handleWindowResize: true,
            headerToolbar: {
                left: (window.innerWidth < 768) ? 'prev,next today' : 'prev,next today',
                center: 'title',
                right: (window.innerWidth < 768) ? 'dayGridMonth,listWeek' : 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: <?php echo json_encode($eventos_calendario); ?>,
            eventClick: function(info) {
                const props = info.event.extendedProps;
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: info.event.title,
                        html: `
                            <div class="text-start">
                                <p><strong><i class="fas fa-hospital me-2"></i>Clínica:</strong> ${props.clinica}</p>
                                <p><strong><i class="fas fa-info-circle me-2"></i>Estado:</strong> <span class="badge ${props.estado === 'Pendiente' ? 'bg-warning' : (props.estado === 'Cancelada' ? 'bg-danger' : 'bg-success')}">${props.estado}</span></p>
                            </div>
                        `,
                        confirmButtonColor: '#1A2D40',
                        confirmButtonText: 'Cerrar'
                    });
                }
            }
        });
        calendar.render();
    });
    </script>
</body>
</html>
>
</html>