<th>Acciones</th>

<td>
    <a href="detalle_mascota.php?id=<?php echo $m['ID_Mascota']; ?>" 
       class="btn btn-primary btn-sm">
        Ver Detalle
    </a>
</td>

<?php
require_once '../../config/db.php';
require_once '../../models/Mascota.php';
require_once '../../config/auth_check.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Validar ID
if (!isset($_GET['id'])) {
    echo "Mascota no encontrada";
    exit();
}

$id_mascota = $_GET['id'];

// Conexión
$database = new Database();
$db = $database->getConnection();

$mascota = new Mascota($db);
$mascota->id_mascota = $id_mascota;

// 🔥 HISTORIAL MÉDICO
$historial = $mascota->verHistorialMedico();

// 🔥 DATOS DE LA MASCOTA
$query = "SELECT * FROM Mascotas WHERE ID_Mascota = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_mascota);
$stmt->execute();

$datos = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Mascota</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver</a>

    <h2>🐾 <?php echo $datos['Nombre']; ?></h2>

    <!-- DATOS -->
    <div class="card p-3 mb-4">
        <h4>Información General</h4>
        <p><b>Especie:</b> <?php echo $datos['Especie']; ?></p>
        <p><b>Raza:</b> <?php echo $datos['Raza']; ?></p>
        <p><b>Edad:</b> <?php echo $datos['Edad']; ?></p>
        <p><b>Peso:</b> <?php echo $datos['Peso']; ?> kg</p>
        <p><b>Color:</b> <?php echo $datos['Color']; ?></p>
    </div>

    <!-- HISTORIAL -->
    <div class="card p-3">
        <h4>📋 Historial Médico</h4>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Diagnóstico</th>
                    <th>Tratamiento</th>
                    <th>Veterinario</th>
                    <th>Clínica</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($historial)): ?>
                <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><?php echo $h['Fecha_Creacion']; ?></td>
                        <td><?php echo $h['Motivo']; ?></td>
                        <td><?php echo $h['Diagnostico_Presuntivo']; ?></td>
                        <td><?php echo $h['Tratamiento_Recomendado']; ?></td>
                        <td><?php echo $h['Nombre_Vet'] . " " . $h['Apellido_Vet']; ?></td>
                        <td><?php echo $h['Clinica']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">
                        No hay historial médico
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>