<?php 
require_once '../../config/auth_check.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella - Portal Veterinario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background-color: #0d6efd; }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link { color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-paw"></i> DocuHuella | Portal Médico</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-bell"></i> Alertas</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-user-circle"></i> Dr. Peña</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="#"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Bienvenido, Dr. Peña</h2>
            <p class="text-muted">Revise su agenda y gestione las historias clínicas.</p>
        </div>
        <div class="col-md-4 text-end">
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Buscar mascota o dueño..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white text-primary fw-bold">
                    <i class="far fa-calendar-check me-2"></i> Mis Citas de Hoy
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div><strong>09:00 AM</strong><br> <small class="text-muted">Bobby (Vacunación)</small></div>
                        <button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div><strong>10:30 AM</strong><br> <small class="text-muted">Luna (Revisión General)</small></div>
                        <button class="btn btn-sm btn-outline-primary">Atender</button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div><strong>01:00 PM</strong><br> <small class="text-muted">Max (Curación)</small></div>
                        <button class="btn btn-sm btn-outline-primary">Atender</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white text-primary fw-bold">
                    <i class="fas fa-stethoscope me-2"></i> Acciones Clínicas
                </div>
                <div class="card-body d-flex gap-3 align-items-start">
                    <button class="btn btn-primary p-3 w-100"><i class="fas fa-plus fa-2x mb-2 d-block"></i> Nueva Historia Clínica</button>
                    <button class="btn btn-secondary p-3 w-100"><i class="fas fa-file-medical fa-2x mb-2 d-block"></i> Buscar Expediente</button>
                    <button class="btn btn-info text-white p-3 w-100"><i class="fas fa-pills fa-2x mb-2 d-block"></i> Recetario</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>