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

// CARGAR ESPECIES PARA EL SELECT
$especies = [];
try {
    $stmtEspecies = $db->prepare("SELECT ID_Especie, Nombre_Especie FROM Especies ORDER BY Nombre_Especie ASC");
    $stmtEspecies->execute();
    $especies = $stmtEspecies->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $especies = [];
}

// CREAR MASCOTA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $imagenRuta = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombreArchivo = time() . "_" . basename($_FILES['imagen']['name']);
        $rutaDestino = "../../uploads/" . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagenRuta = "uploads/" . $nombreArchivo;
        }
    }

    $especieSeleccionada = 'No especificada';

    if (!empty($_POST['especie'])) {
        $stmtNombreEspecie = $db->prepare("SELECT Nombre_Especie FROM Especies WHERE ID_Especie = :id LIMIT 1");
        $stmtNombreEspecie->execute([':id' => $_POST['especie']]);
        $filaEspecie = $stmtNombreEspecie->fetch(PDO::FETCH_ASSOC);

        if ($filaEspecie) {
            $especieSeleccionada = $filaEspecie['Nombre_Especie'];
        }
    }

    $query = "INSERT INTO Mascotas 
    (Nombre, Especie, Raza, Sexo, Color, Edad, Rasgos, Peso, Estado_Esterilizacion, ID_Cuidador, Imagen)
    VALUES (:nombre, :especie, 'No especificada', :sexo, :color, :edad, '', :peso, 'No', :id_cuidador, :imagen)";

    $stmt = $db->prepare($query);

    $stmt->execute([
        ':nombre' => $_POST['nombre'],
        ':especie' => $especieSeleccionada,
        ':sexo' => $_POST['sexo'],
        ':color' => 'No especificado',
        ':edad' => 0,
        ':peso' => 0,
        ':id_cuidador' => $_SESSION['id_perfil'],
        ':imagen' => $imagenRuta
    ]);

    header("Location: dashboard.php");
    exit();
}

//$mascotas = $cuidador->verMisMascotas();

$mascotaSeleccionada = null;
$historial = [];

if (isset($_GET['mascota'])) {
    $id = $_GET['mascota'];

    $stmt = $db->prepare("SELECT * FROM Mascotas WHERE ID_Mascota = :id");
    $stmt->execute([':id' => $id]);
    $mascotaSeleccionada = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($mascotaSeleccionada) {
        $m = new Mascota($db);
        $m->id_mascota = $id;
        $historial = $m->verHistorialMedico();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Cuidador</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #ffffff;
}

.sidebar {
    width: 250px;
    min-height: 100vh;
    background: #1A2D40;
    padding: 20px;
    color: white;
    display: flex;
    flex-direction: column;
}

.sidebar h4 {
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
    color: white;
}

.sidebar hr {
    border-color: rgba(255, 255, 255, 0.2);
}

.sidebar a,
.sidebar button {
    display: block;
    color: white;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 5px;
    text-decoration: none;
    border: none;
    background: transparent;
    text-align: left;
    width: 100%;
    transition: 0.2s;
}

.sidebar a:hover,
.sidebar button:hover {
    background: #2c3e50;
    color: white;
}

.user-badge {
    background: #f1c40f;
    color: black;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: bold;
    display: block;
    width: 100%;
    text-align: left;
    margin: 0 auto 15px auto;
}

.logout-btn {
    background: #dc3545 !important;
    color: white !important;
    border: none;
    width: 100%;
    margin-top: auto;
}

.main {
    flex-grow: 1;
    padding: 20px;
    background: #ffffff;
    min-height: 100vh;
}

.card-main {
    background: #1A2D40;
    color: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.20);
}

.card-main h3,
.card-main h5,
.card-main p,
.card-main label {
    color: white;
}

.info-box {
    background: white;
    color: black;
    padding: 20px;
    border-radius: 15px;
    height: 100%;
}

.info-box p,
.info-box b {
    color: black;
    font-size: 16px;
}

.img-box {
    background: white;
    border-radius: 15px;
    padding: 15px;
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.img-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
    border-radius: 10px;
}

.card {
    background: white !important;
    color: black !important;
    border-radius: 15px;
    border: none;
}

.card h5,
.card p,
.card td,
.card th {
    color: black !important;
}

.table {
    background: white;
    color: black;
}

.table thead th {
    background: #f8f9fa;
    color: black;
}

.table tbody td {
    background: white;
    color: black;
    vertical-align: middle;
}

.form-control,
.form-select,
select,
input[type="file"] {
    background: white !important;
    color: black !important;
    border: 1px solid #ccc;
}

.form-control:focus,
.form-select:focus,
select:focus {
    background: white !important;
    color: black !important;
    box-shadow: none;
    border-color: #86b7fe;
}

.btn-dark {
    background: white;
    color: #1A2D40;
    border: none;
    font-weight: bold;
}

.btn-dark:hover {
    background: #f1f1f1;
    color: #1A2D40;
}
</style>
</head>

<body>

<div class="d-flex">

    <div class="sidebar">
        <h4>🐾 DocuHuella</h4>

        <span class="user-badge">
            <?php echo $_SESSION['nombre']; ?>
        </span>

        <hr>

        <a href="dashboard.php">🏠 Inicio</a>

        <button type="button" data-bs-toggle="collapse" data-bs-target="#menuMascotas" aria-expanded="true" aria-controls="menuMascotas">
            🐶 Mascotas
        </button>

        <div id="menuMascotas" class="collapse show">
            <?php foreach ($mascotas as $m): ?>
                <a href="?mascota=<?php echo $m['ID_Mascota']; ?>">
                    • <?php echo $m['Nombre']; ?>
                </a>
            <?php endforeach; ?>

            <a href="?nueva=1">➕ Nueva Mascota</a>
        </div>

        <form action="../../controllers/UsuariosController.php?action=logout" method="POST" class="mt-auto">
            <button type="submit" class="logout-btn btn btn-danger">Cerrar Sesión</button>
        </form>
    </div>

    <div class="main">

        <?php if (isset($_GET['nueva'])): ?>

            <div class="card-main">
                <h3>Nueva Mascota</h3>

                <form method="POST" enctype="multipart/form-data">
                    <input class="form-control mb-3" name="nombre" placeholder="Nombre" required>

                    <select class="form-control mb-3" name="especie" required>
                        <option value="">Seleccione una especie</option>
                        <?php foreach ($especies as $esp): ?>
                            <option value="<?php echo $esp['ID_Especie']; ?>">
                                <?php echo $esp['Nombre_Especie']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="form-control mb-3" name="sexo">
                        <option value="Macho">Macho</option>
                        <option value="Hembra">Hembra</option>
                    </select>

                    <input type="file" name="imagen" class="form-control mb-3">

                    <button type="submit" class="btn btn-dark">Guardar</button>
                </form>
            </div>

        <?php elseif ($mascotaSeleccionada): ?>

            <div class="card-main">
                <h3><?php echo $mascotaSeleccionada['Nombre']; ?></h3>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="info-box">
                            <p><b>Especie:</b> <?php echo $mascotaSeleccionada['Especie']; ?></p>
                            <p><b>Edad:</b> <?php echo $mascotaSeleccionada['Edad']; ?></p>
                            <p><b>Sexo:</b> <?php echo $mascotaSeleccionada['Sexo']; ?></p>
                            <p><b>Color:</b> <?php echo $mascotaSeleccionada['Color']; ?></p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="img-box">
                            <img 
                                src="<?php echo (!empty($mascotaSeleccionada['Imagen'])) ? '../../' . $mascotaSeleccionada['Imagen'] : 'https://placedog.net/400/300'; ?>" 
                                alt="Imagen de mascota">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card p-3">
                        <h5>Historial Médico</h5>

                        <table class="table">
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
                                            <td><?php echo $h['Fecha_Hora']; ?></td>
                                            <td><?php echo $h['Motivo']; ?></td>
                                            <td><?php echo $h['Diagnostico_Presuntivo']; ?></td>
                                            <td>
                                                <?php
                                                $nombreVet = trim(($h['Nombre_Vet'] ?? '') . ' ' . ($h['Apellido_Vet'] ?? ''));
                                                echo $nombreVet !== '' ? $nombreVet : 'N/D';
                                                ?>
                                            </td>
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