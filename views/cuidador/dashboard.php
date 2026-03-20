<?php
require_once '../../config/auth_check.php';
session_start();

$rol_permitido = 3; // Rol permitido para acceder a esta página (Cuidador)

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
    <title>Panel - DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark" style="background-color: #1A2D40;">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
                <i class="fas fa-paw me-2"></i>DocuHuella
            </a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Conectado como: <b><?php echo $_SESSION['correo']; ?></b></span>
                <a href="../../controllers/UsuariosController.php?action=logout" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h1 class="display-5 fw-bold text-secondary">Bienvenido al Panel</h1>
        <p class="lead">Estás en la zona segura. Solo el Rol <?php echo $rol_permitido; ?> puede ver esto.</p>
    </div>

</body>
</html>