<?php

require_once '../../config/db.php';
require_once '../../models/Cuidador.php';
require_once '../../models/Mascota.php';
require_once '../../config/auth_check.php';

$rol_permitido = 3;

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != $rol_permitido) {
    header("Location: ../login.php");
    exit();
}

// 🔥 CONEXIÓN
$database = new Database();
$db = $database->getConnection();

// 🔥 CUIDADOR
$cuidador = new Cuidador($db);
$cuidador->id_cuidador = $_SESSION['id_perfil'];

// 🔥 MASCOTAS
$mascotas = $cuidador->verMisMascotas();

// 🔥 MASCOTA SELECCIONADA
$mascotaSeleccionada = null;
$historial = [];

if (isset($_GET['mascota'])) {
    $id = $_GET['mascota'];

    $query = "SELECT * FROM Mascotas WHERE ID_Mascota = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $mascotaSeleccionada = $stmt->fetch(PDO::FETCH_ASSOC);

    $mascotaObj = new Mascota($db);
    $mascotaObj->id_mascota = $id;
    $historial = $mascotaObj->verHistorialMedico();
}
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
body {
    overflow-x: hidden;
    background-color: #1A2D40;
}

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
    border-radius: 8px;
}

.sidebar a:hover {
    background-color: #2c3e50;
}

.mascota-link {
    padding-left: 25px;
    font-size: 14px;
}

.contenido-box {
    background-color: #D9C9B0;
    border-radius: 15px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark" style="background-color: #1A2D40;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold">
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

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="sidebar p-3" style="width:250px;">

        <h5 class="mb-4 text-center">Menú</h5>

        <a href="#"><i class="fas fa-home"></i> Inicio</a>

        <!-- DESPLEGABLE -->
        <button class="btn btn-dark w-100 text-start mt-2" data-bs-toggle="collapse" data-bs-target="#menuMascotas">
            <i class="fas fa-dog"></i> Mascotas
        </button>

        <div id="menuMascotas" class="collapse mt-2">

            <?php if (!empty($mascotas)): ?>
                <?php foreach ($mascotas as $m): ?>
                    <a href="?mascota=<?php echo $m['ID_Mascota']; ?>" class="mascota-link">
                        🐾 <?php echo $m['Nombre']; ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="mascota-link">Sin mascotas</span>
            <?php endif; ?>

        </div>

        <a href="#" class="mt-2"><i class="fas fa-notes-medical"></i> Actividades</a>

    </div>

    <!-- CONTENIDO -->
    <div class="flex-grow-1 p-4">

        <?php if ($mascotaSeleccionada): ?>

        <div class="contenido-box p-4">

            <h4 class="mb-4">
                Bienvenido <?php echo $_SESSION['nombre']; ?>
            </h4>

            <div class="row">

                <!-- INFO -->
                <div class="col-md-6">
                    <div class="p-4 rounded" style="background-color:#1A2D40; color:white;">

                        <p><b>Nombre:</b> <?php echo $mascotaSeleccionada['Nombre']; ?></p>
                        <p><b>Raza:</b> <?php echo $mascotaSeleccionada['Raza']; ?></p>
                        <p><b>Edad:</b> <?php echo $mascotaSeleccionada['Edad']; ?></p>
                        <p><b>Sexo:</b> <?php echo $mascotaSeleccionada['Sexo']; ?></p>
                        <p><b>Color:</b> <?php echo $mascotaSeleccionada['Color']; ?></p>

                    </div>
                </div>

                <!-- IMAGEN -->
                <div class="col-md-6 text-center">

                    <div class="p-3 rounded" style="background-color:#1A2D40; display:inline-block;">
                        
                        <img src="https://placedog.net/400/300"
                             class="img-fluid rounded"
                             style="max-height:250px;">

                    </div>

                </div>

            </div>

            <!-- HISTORIAL -->
            <div class="mt-4">

                <div class="card p-3 shadow-sm">
                    <h5>📋 Historial Médico</h5>

                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
                                <th>Veterinario</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($historial)): ?>
                            <?php foreach ($historial as $h): ?>
                                <tr>
                                    <td><?php echo $h['Fecha_Creacion']; ?></td>
                                    <td><?php echo $h['Motivo']; ?></td>
                                    <td><?php echo $h['Diagnostico_Presuntivo']; ?></td>
                                    <td><?php echo $h['Nombre_Vet']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Sin historial</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <?php else: ?>

        <!-- PANTALLA INICIAL -->
        <div class="contenido-box p-4 text-center">
            <h3>👋 Bienvenido</h3>
            <p>Selecciona una mascota en el menú para ver su información</p>
        </div>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>