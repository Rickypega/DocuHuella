<?php

require_once '../../config/db.php';
require_once '../../models/Cuidador.php';
require_once '../../config/auth_check.php';

$rol_permitido = 3;

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != $rol_permitido) {
    header("Location: ../login.php");
    exit();
}

// 🔥 CONEXIÓN A BD
$database = new Database();
$db = $database->getConnection();

// 🔥 INSTANCIA CUIDADOR
$cuidador = new Cuidador($db);
$cuidador->id_cuidador = $_SESSION['id_perfil'];

// 🔥 OBTENER MASCOTAS
$mascotas = $cuidador->verMisMascotas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - DocuHuella</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .sidebar {
            height: 100vh;
            background-color: #1A2D40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: #2c3e50;
        }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark" style="background-color: #1A2D40;">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold" href="#">
            <i class="fas fa-paw me-2"></i>DocuHuella
        </a>

        <div class="d-flex text-white align-items-center">
            <span class="me-3">
                Bienvenido: <b><?php echo $_SESSION['nombre']; ?></b>
            </span>
            <a href="../../controllers/UsuariosController.php?action=logout" class="btn btn-outline-light btn-sm">
                Cerrar Sesión
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
<div class="row">

    <!-- SIDEBAR -->
    <div class="col-md-2 sidebar p-3">
        <h5 class="text-center">Menú</h5>

        <a href="#"><i class="fas fa-home"></i> Inicio</a>

        <a href="../../controllers/CuidadorController.php?action=mascotas">
            <i class="fas fa-dog"></i> Mis Mascotas
        </a>

        <a href="#"><i class="fas fa-notes-medical"></i> Actividades</a>
    </div>

    <!-- CONTENIDO -->
    <div class="col-md-10 p-4">

        <h2 class="mb-4">Panel del Cuidador</h2>

        <!-- TARJETAS -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h5><i class="fas fa-dog"></i> Mascotas</h5>
                    <p><?php echo count($mascotas); ?> registradas</p>
                </div>
            </div>
        </div>

        <!-- TABLA DE MASCOTAS -->
        <div class="card p-3 shadow-sm">
            <h4>🐾 Mis Mascotas</h4>

            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Especie</th>
                        <th>Raza</th>
                        <th>Edad</th>
                        <th>Peso</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!empty($mascotas)): ?>
                    <?php foreach ($mascotas as $m): ?>
                        <tr>
                            <td><?php echo $m['Nombre']; ?></td>
                            <td><?php echo $m['Especie']; ?></td>
                            <td><?php echo $m['Raza']; ?></td>
                            <td><?php echo $m['Edad']; ?></td>
                            <td><?php echo $m['Peso']; ?> kg</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            No tienes mascotas registradas
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>
</div>

</body>
</html>