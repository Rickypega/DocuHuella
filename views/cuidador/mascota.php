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
                  FROM expedientes e
                  INNER JOIN veterinarios v ON e.ID_Veterinario = v.ID_Veterinario
                  INNER JOIN clinicas c ON v.ID_Clinica = c.ID_Clinica
                  WHERE e.ID_Mascota = :id 
                  ORDER BY e.Fecha_Hora DESC";

$stmtHistorial = $db->prepare($queryHistorial);
$stmtHistorial->bindParam(':id', $id_mascota);
$stmtHistorial->execute();

$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

// 🔥 DATOS DE LA MASCOTA
$query = "SELECT * FROM mascotas WHERE ID_Mascota = :id";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuHuella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= URL_BASE ?>/public/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo-container">
            <h3 class="fw-bold text-white mb-0"><i class="fas fa-paw" style="color: var(--dh-beige);"></i> DocuHuella</h3>
            <span class="badge bg-success text-white mt-2">Cuidador</span>
        </div>

        <nav class="mt-3">
            <a href="<?= URL_BASE ?>/cuidador/dashboard"><i class="fas fa-home"></i> Mi Panel</a>
            <a href="<?= URL_BASE ?>/views/cuidador/mis_mascotas.php" class="active"><i class="fas fa-bone"></i> Mis Mascotas</a>
        </nav>
        
        <div class="mt-auto">
            <a href="#" class="btn btn-outline-light d-flex align-items-center justify-content-center gap-2 mb-2" style="border-radius: 10px; padding: 12px; margin: 0 15px; border-color: rgba(255,255,255,0.2);" data-bs-toggle="modal" data-bs-target="#modalPerfilGlobal">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="<?= URL_BASE ?>/logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; padding: 12px; margin: 0 15px 20px; width: auto !important;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">

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
                    <img src="<?= URL_BASE ?>/<?php echo $datos['Imagen']; ?>" 
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include_once APP_PATH . '/views/includes/modal_perfil.php'; ?>
</body>
</html>
