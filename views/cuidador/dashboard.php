<?php

require_once '../../config/db.php';
require_once '../../models/Cuidador.php';
require_once '../../models/Mascota.php';
require_once '../../config/auth_check.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$cuidador = new Cuidador($db);
$cuidador->id_cuidador = $_SESSION['id_perfil'];

// CREAR MASCOTA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $imagenRuta = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombreArchivo = time() . "_" . $_FILES['imagen']['name'];
        $rutaDestino = "../../uploads/" . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagenRuta = "uploads/" . $nombreArchivo;
        }
    }

    $query = "INSERT INTO Mascotas 
    (Nombre, Especie, Raza, Sexo, Color, Edad, Rasgos, Peso, Estado_Esterilizacion, ID_Cuidador, Imagen)
    VALUES (:nombre, :especie, 'No especificada', :sexo, :color, :edad, '', :peso, 'No', :id_cuidador, :imagen)";

    $stmt = $db->prepare($query);

    $stmt->execute([
        ':nombre' => $_POST['nombre'],
        ':especie' => $_POST['especie'],
        ':sexo' => $_POST['sexo'],
        ':color' => $_POST['color'],
        ':edad' => $_POST['edad'],
        ':peso' => $_POST['peso'],
        ':id_cuidador' => $_SESSION['id_perfil'],
        ':imagen' => $imagenRuta
    ]);
}

$mascotas = $cuidador->verMisMascotas();

$mascotaSeleccionada = null;
$historial = [];

if (isset($_GET['mascota'])) {
    $id = $_GET['mascota'];

    $stmt = $db->prepare("SELECT * FROM Mascotas WHERE ID_Mascota = :id");
    $stmt->execute([':id' => $id]);
    $mascotaSeleccionada = $stmt->fetch(PDO::FETCH_ASSOC);

    $m = new Mascota($db);
    $m->id_mascota = $id;
    $historial = $m->verHistorialMedico();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Cuidador</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body {
    background-color: #1F3446;
    color: white;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    height: 100vh;
    background: #1A2D40;
    padding: 20px;
}

.sidebar h4 {
    font-weight: bold;
}

.sidebar a, .sidebar button {
    display: block;
    color: white;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 5px;
    text-decoration: none;
}

.sidebar a:hover {
    background: #2c3e50;
}

/* BOTÓN ROJO */
.logout-btn {
    background: #dc3545;
    border: none;
    width: 100%;
    margin-top: 20px;
}

/* CONTENIDO */
.main {
    flex-grow: 1;
    padding: 20px;
}

/* TARJETA */
.card-main {
    background: #D9C9B0;
    color: black;
    border-radius: 20px;
    padding: 20px;
}

/* INFO BOX */
.info-box {
    background: #1A2D40;
    color: white;
    border-radius: 15px;
    padding: 20px;
}

/* BADGE USER */
.user-badge {
    background: #f1c40f;
    color: black;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: bold;
}

</style>
</head>

<body>

<div class="d-flex">

<!-- SIDEBAR -->
<div class="sidebar">

    <h4>🐾 DocuHuella</h4>

    <span class="user-badge">
        <?php echo $_SESSION['nombre']; ?>
    </span>

    <hr>

    <a href="#">🏠 Inicio</a>

    <button data-bs-toggle="collapse" data-bs-target="#menuMascotas">
        🐶 Mascotas
    </button>

    <div id="menuMascotas" class="collapse">
        <?php foreach ($mascotas as $m): ?>
            <a href="?mascota=<?php echo $m['ID_Mascota']; ?>">
                • <?php echo $m['Nombre']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <a href="?nueva=1">➕ Nueva Mascota</a>

    <form action="../../controllers/UsuariosController.php?action=logout" method="POST">
        <button class="logout-btn btn btn-danger">Cerrar Sesión</button>
    </form>

</div>

<!-- CONTENIDO -->
<div class="main">

<?php if (isset($_GET['nueva'])): ?>

<div class="card-main">
    <h3>Nueva Mascota</h3>

    <form method="POST" enctype="multipart/form-data">

        <input class="form-control mb-2" name="nombre" placeholder="Nombre" required>
        <input class="form-control mb-2" name="especie" placeholder="Especie" required>
        <input class="form-control mb-2" name="edad" placeholder="Edad" required>
        <input class="form-control mb-2" name="peso" placeholder="Peso" required>
        <input class="form-control mb-2" name="color" placeholder="Color" required>

        <select class="form-control mb-2" name="sexo">
            <option>Macho</option>
            <option>Hembra</option>
        </select>

        <input type="file" name="imagen" class="form-control mb-3">

        <button class="btn btn-dark">Guardar</button>

    </form>
</div>

<?php elseif ($mascotaSeleccionada): ?>

<div class="card-main">

    <h3><?php echo $mascotaSeleccionada['Nombre']; ?></h3>

    <div class="row">

        <div class="col-md-6">
            <div class="info-box">
                <p><b>Edad:</b> <?php echo $mascotaSeleccionada['Edad']; ?></p>
                <p><b>Sexo:</b> <?php echo $mascotaSeleccionada['Sexo']; ?></p>
                <p><b>Color:</b> <?php echo $mascotaSeleccionada['Color']; ?></p>
            </div>
        </div>

        <div class="col-md-6 text-center">
            <img 
            src="<?php echo (!empty($mascotaSeleccionada['Imagen'])) 
                ? '../../' . $mascotaSeleccionada['Imagen'] 
                : 'https://placedog.net/400/300'; ?>"
            class="img-fluid rounded"
            style="max-height:250px;">
        </div>

    </div>

</div>

<?php else: ?>

<div class="card-main text-center">
    <h3>Bienvenido</h3>
    <p>Selecciona una mascota</p>
</div>

<?php endif; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>