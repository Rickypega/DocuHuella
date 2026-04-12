<?php 
include_once '../config/db.php';
include_once '../layouts/header.php';
?>

<div class="container my-5 py-5" style="min-height: 70vh;">
    <div class="card shadow-sm p-4 border-0">
        <h1 class="fw-bold text-navy"><i class="fas fa-user-shield me-2"></i>Política de Privacidad</h1>
        <hr>
        
        <div class="alert alert-warning border-0 shadow-sm" role="alert">
            <i class="fas fa-university me-2"></i>
            <strong>AVISO IMPORTANTE:</strong> Este sitio web es parte de un proyecto estudiantil para la asignatura de INF-455 PROYECTO III en la <strong>Universidad Católica Tecnológica de Barahona (UCATEBA)</strong>. No es una plataforma comercial ni una empresa prestadora de servicios reales.
        </div>

        <h3 class="mt-4">1. Recopilación de Datos</h3>
        <p>Los datos solicitados (nombre, correo electrónico, datos de mascotas e historiales clínicos) se recopilan exclusivamente con fines de evaluación académica y pruebas de funcionamiento del software <strong>DocuHuella</strong>.</p>

        <h3>2. Uso de la Información</h3>
        <p>La información almacenada en este sistema se utiliza para demostrar la capacidad técnica del manejo de bases de datos y seguridad informática. No se enviará publicidad ni se venderá información a terceros.</p>

        <h3>3. Seguridad</h3>
        <p>Aunque el sistema implementa medidas de seguridad se recomienda <strong>no utilizar contraseñas reales</strong> que el usuario use en otros sitios personales (correos, bancos, etc.).</p>

        <h3>4. Eliminación de Datos</h3>
        <p>Toda la información contenida en la base de datos podrá ser eliminada en cualquier momento una vez concluido el periodo académico de evaluación en la universidad.</p>
        
        <div class="mt-5 text-center">
            <a href="../index.php" class="btn btn-dh">Volver al Inicio</a>
        </div>
    </div>
</div>

<?php include_once '../layouts/footer.php'; ?>