<?php
require_once '../../config/auth_check.php';


$rol_permitido = 1; // Rol permitido para acceder a esta página (Admin)

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != $rol_permitido) {
    // Si no ha iniciado sesión o no es el rol correcto, lo devolvemos al login
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 10px 15px; display: block; }
        .sidebar a:hover { color: white; background-color: #495057; }
        .sidebar .active { color: white; background-color: #0d6efd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar py-3">
            <h4 class="text-center mb-4"><i class="fas fa-paw"></i> DocuHuella</h4>
            <a href="#" class="active"><i class="fas fa-tachometer-alt me-2"></i> Resumen</a>
            <a href="#"><i class="fas fa-user-md me-2"></i> Personal Veterinario</a>
            <a href="#"><i class="fas fa-users me-2"></i> Usuarios del Sistema</a>
            <a href="#"><i class="fas fa-chart-bar me-2"></i> Reportes Clínicos</a>
            <a href="#"><i class="fas fa-cogs me-2"></i> Configuración</a>
            <a href="#" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Panel de Administración</h1>
                <div class="text-muted">Hola, Admin (Ricky)</div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <h5 class="card-title">Pacientes Registrados</h5>
                            <h2 class="card-text">1,245</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <h5 class="card-title">Consultas de Hoy</h5>
                            <h2 class="card-text">24</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <h5 class="card-title">Veterinarios Activos</h5>
                            <h2 class="card-text">8</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">Actividad Reciente del Sistema</div>
                <div class="card-body text-center text-muted">
                    <p>Aquí cargaremos la tabla de actividad desde el controlador...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>