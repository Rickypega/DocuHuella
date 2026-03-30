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

// 🔥 HISTORIAL MÉDICO (YA CORREGIDO)
$queryHistorial = "SELECT 
                    e.ID_Expediente, 
                    e.Fecha_Hora AS Fecha_Creacion,
                    e.Motivo, 
                    e.Diagnostico_Presuntivo, 
                    e.Tratamiento_Recomendado,
                    v.Nombre AS Nombre_Vet, 
                    v.Apellido AS Apellido_Vet,
                    c.Nombre_Sucursal AS Clinica
                  FROM Expedientes e
                  INNER JOIN Veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN Clinicas c ON v.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id 
                  ORDER BY e.Fecha_Hora DESC";

$stmtHistorial = $db->prepare($queryHistorial);
$stmtHistorial->bindParam(':id', $id_mascota);
$stmtHistorial->execute();

$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

// 🔥 DATOS DE LA MASCOTA
$query = "SELECT * FROM Mascotas WHERE ID_Mascota = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_mascota);
$stmt->execute();

$datos = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar que exista
if (!$datos) {
    echo "Mascota no encontrada";
    exit();
}
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

        <div class="row">
            <div class="col-md-6">
                <p><b>Especie:</b> <?php echo $datos['Especie']; ?></p>
                <p><b>Raza:</b> <?php echo $datos['Raza']; ?></p>
                <p><b>Edad:</b> <?php echo $datos['Edad']; ?></p>
                <p><b>Peso:</b> <?php echo $datos['Peso']; ?> kg</p>
                <p><b>Color:</b> <?php echo $datos['Color']; ?></p>
            </div>

            <div class="col-md-6 text-center">
                <?php if (!empty($datos['Imagen'])): ?>
                    <img src="../../<?php echo $datos['Imagen']; ?>" 
                         class="img-fluid rounded"
                         style="max-height:250px;">
                <?php else: ?>
                    <img src="https://placedog.net/400/300"
                         class="img-fluid rounded"
                         style="max-height:250px;">
                <?php endif; ?>
            </div>
        </div>
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