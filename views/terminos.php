<?php 
include_once 'config/db.php';
include_once 'layouts/header.php';
?>

<div class="container my-5 py-5" style="min-height: 70vh;">
    <div class="card shadow-sm p-4 border-0">
        <h1 class="fw-bold text-navy"><i class="fas fa-file-contract me-2"></i>Términos y Condiciones de Uso</h1>
        <hr>

        <div class="alert alert-info border-0 shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>PROYECTO SIN FINES DE LUCRO:</strong> El uso de esta plataforma es estrictamente pedagógico y demostrativo.
        </div>

        <h3 class="mt-4">1. Exención de Responsabilidad Legal</h3>
        <p>Los desarrolladores y la institución (UCATEBA) no asumen <strong>ninguna responsabilidad legal, médica o civil</strong> por el uso que se le dé a esta herramienta. Los datos médicos y prescripciones generadas aquí son simulados y no deben ser seguidos en la vida real para el tratamiento de animales.</p>

        <h3>2. Veracidad de la Información</h3>
        <p>El usuario acepta que los diagnósticos y reportes generados por el sistema son parte de una simulación de software. En caso de una emergencia real con una mascota, el usuario debe acudir a una clínica veterinaria profesional certificada.</p>

        <h3>3. Disponibilidad del Servicio</h3>
        <p>Al ser un despliegue en un hosting gratuito (InfinityFree/WebHostMost) para fines de revisión de proyecto, la plataforma puede presentar interrupciones, pérdida de datos o ser dada de baja sin previo aviso.</p>

        <h3>4. Compromiso del Usuario</h3>
        <p>Al registrarse en <strong>DocuHuella</strong>, usted reconoce que ha sido informado sobre el carácter educativo de esta web y acepta interactuar con ella bajo estas condiciones.</p>

        <div class="mt-5 d-flex justify-content-between align-items-center">
            <a href="<?= URL_BASE ?>/" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Inicio</a>
            <a href="<?= URL_BASE ?>/privacidad" class="btn btn-dh"><i class="fas fa-user-shield me-2"></i>Ver Política de Privacidad</a>
        </div>
    </div>
</div>

<?php include_once 'layouts/footer.php'; ?>