<?php 
session_start();

// SEGURIDAD: Solo el SuperAdmin entra aquí
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 4) {
    header("Location: ../login.php?error=acceso_denegado");
    exit();
}

require_once '../../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Obtener el rol épico de la base de datos para el saludo
$nombre_rol = "Super Admin";
try {
    $stmt_rol = $db->prepare("SELECT Nombre_Rol FROM Roles WHERE ID_Rol = :id_rol");
    $stmt_rol->bindParam(':id_rol', $_SESSION['id_rol']);
    $stmt_rol->execute();
    $resultado_rol = $stmt_rol->fetchColumn();
    if ($resultado_rol) $nombre_rol = $resultado_rol;
} catch (PDOException $e) {}

// CONSULTA MAESTRA
$query = "SELECT a.ID_Admin, a.Nombre, a.Apellido, a.Telefono, u.Correo, c.Nombre_Sucursal, c.RNC 
          FROM administrador a
          INNER JOIN usuarios u ON a.ID_Usuario = u.ID_Usuario
          LEFT JOIN clinicas c ON a.ID_Admin = c.ID_Admin
          ORDER BY a.ID_Admin DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clínicas - DocuHuella</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --dh-beige: #c5aa7f;
            --dh-navy: #1A2D40;
            --dh-light-gray: #F8F9FA;
        }

        body { background-color: var(--dh-light-gray); overflow-x: hidden; font-family: 'Segoe UI', Tahoma, sans-serif; }

        /* Diseño del Sidebar Genérico */
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

        .sidebar i { width: 25px; text-align: center; margin-right: 10px; }

        /* Botón de Logout Genérico */
        .btn-logout {
            background-color: #dc3545; 
            color: white !important; 
            margin: auto 15px 20px; 
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

        .card-dh {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: none;
        }

        .btn-dh-navy { background-color: var(--dh-navy); color: white; border-radius: 8px; border: none; padding: 10px 20px; }
        .btn-dh-navy:hover { background-color: #253d56; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-warning text-dark mt-2">Super Admin</span>
        </div>

        <nav class="mt-3">
            <a href="../../controllers/superadmin/DashboardController.php?action=ver"><i class="fas fa-chart-pie"></i> Estadísticas</a>
            <a href="../../views/superadmin/administrador.php" class="active"><i class="fas fa-hospital"></i> Gestión de Clínicas</a>
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
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0" style="color: var(--dh-navy);">Administradores de Franquicias</h2>
                <p class="text-muted">Gestión y control de clientes</p>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-dh-navy shadow" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                <i class="fas fa-plus-circle me-2"></i> Nueva Franquicia
            </button>
        </div>

        <div class="card-dh mt-4">
            <div class="table-responsive">
                <table id="tablaAdmins" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Administrador</th>
                            <th>Teléfono</th>
                            <th>Clínica</th>
                            <th>RNC</th>
                            <th>Correo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($admins as $admin): ?>
                        <tr>
                            <td><strong>#<?php echo $admin['ID_Admin']; ?></strong></td>
                            <td><?php echo htmlspecialchars($admin['Nombre'] . " " . $admin['Apellido']); ?></td>
                            <td><?php echo htmlspecialchars($admin['Telefono'] ?? '---'); ?></td>
                            <td>
                                <span class="badge <?php echo isset($admin['Nombre_Sucursal']) ? 'bg-info text-dark' : 'bg-danger'; ?>">
                                    <?php echo htmlspecialchars($admin['Nombre_Sucursal'] ?? 'Sin Clínica'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($admin['RNC'] ?? '---'); ?></td>
                            <td class="text-muted small"><?php echo htmlspecialchars($admin['Correo']); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form action="../../controllers/superadmin/AdminController.php?action=registrar" method="POST">
                    <div class="modal-header text-white" style="background-color: var(--dh-navy); border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title"><i class="fas fa-store-alt me-2"></i> Registrar Nueva Franquicia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2">1. Credenciales y Perfil</h6>
                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña Temporal</label>
                                <input type="password" name="contrasena" class="form-control" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" required placeholder="Ej: 809-555-0000">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula" class="form-control" required>
                            </div>

                            <h6 class="text-uppercase fw-bold text-muted border-bottom pb-2 mt-4">2. Información de la Clínica</h6>
                            <div class="col-md-6">
                                <label class="form-label">Nombre de la Veterinaria</label>
                                <input type="text" name="nombre_clinica" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">RNC</label>
                                <input type="text" name="rnc" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Dirección Física</label>
                                <input type="text" name="direccion_clinica" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dh-navy px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

   <script>
        $(document).ready(function() {
            $('#tablaAdmins').DataTable({
                "language": { 
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
                    
                    "info": "Mostrando página _PAGE_ de _PAGES_ con un total de _TOTAL_ registros", 
                    
                    // Personalización adicional para casos sin datos
                    "infoEmpty": "Mostrando página 0 de 0 con un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)"
                },
                "pageLength": 10
            });
        });
    </script>
</body>
</html>